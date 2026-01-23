@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Layanan: {{ $service->name }}</h1>
            <p class="text-gray-600">Update detail layanan dan kelola paket-paketnya</p>
        </div>
        <a href="{{ route('admin.services.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Edit Service Form -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-cog text-indigo-600 mr-2"></i>
            Detail Layanan
        </h3>
        
        <form action="{{ route('admin.services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $service->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga Dasar <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="base_price" id="basePrice" value="{{ old('base_price', number_format($service->base_price, 0, ',', '.')) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                           placeholder="1.500.000">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $service->description) }}</textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $service->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ ucfirst($category->division) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Layanan Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-save mr-2"></i>Update Layanan
                </button>
            </div>
        </form>
    </div>

    <!-- Packages Management -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-box text-purple-600 mr-2"></i>
                Paket Layanan ({{ $service->packages->count() }})
            </h3>
            <button onclick="showAddPackageModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-plus mr-2"></i>Tambah Paket
            </button>
        </div>
        
        @if($service->packages->count() > 0)
        <div class="space-y-3">
            @foreach($service->packages as $package)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition" x-data="{ editing: false }">
                <!-- View Mode -->
                <div x-show="!editing">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-gray-900">{{ $package->name }}</h4>
                                @if(!$package->is_active)
                                <span class="px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-600 rounded-full">Nonaktif</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $package->description ?? 'Tidak ada deskripsi' }}</p>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="text-lg font-bold text-purple-600">
                                    Rp {{ number_format($package->price, 0, ',', '.') }}
                                </div>
                                @if($package->duration_days)
                                <div class="text-sm text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>{{ $package->duration_days }} hari
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button @click="editing = true" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <form action="{{ route('admin.packages.destroy', $package) }}" method="POST" 
                                  onsubmit="return confirm('Yakin hapus paket ini?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Edit Mode -->
                <form x-show="editing" action="{{ route('admin.packages.update', $package) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Paket</label>
                            <input type="text" name="name" value="{{ $package->name }}" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga</label>
                            <input type="text" name="price" value="{{ number_format($package->price, 0, ',', '.') }}" required
                                   class="price-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                   placeholder="1.500.000">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">{{ $package->description }}</textarea>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Durasi (hari)</label>
                                <input type="number" name="duration_days" value="{{ $package->duration_days }}" min="1"
                                       class="w-32 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                            <label class="flex items-center cursor-pointer mt-4">
                                <input type="checkbox" name="is_active" value="1" {{ $package->is_active ? 'checked' : '' }}
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-2 text-xs font-medium text-gray-700">Aktif</span>
                            </label>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="editing = false" class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit" class="px-3 py-1 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-box-open text-4xl mb-2 text-gray-300"></i>
            <p>Belum ada paket untuk layanan ini</p>
        </div>
        @endif
    </div>
</div>

<!-- Add Package Modal -->
<div id="addPackageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-plus-circle mr-2"></i>Tambah Paket Baru
            </h3>
            <button onclick="closeAddPackageModal()" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.services.packages.store', $service) }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Paket <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Paket Silver">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="price" id="newPackagePrice" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="1.500.000">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Deskripsi paket..."></textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Durasi (hari)
                    </label>
                    <input type="number" name="duration_days" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="30">
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeAddPackageModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Paket
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Format price on blur
    function formatPrice(input) {
        const value = input.value.replace(/\./g, '');
        if (value && !isNaN(value)) {
            input.value = parseInt(value).toLocaleString('id-ID');
        }
    }
    
    // Auto format on blur for all price inputs
    document.getElementById('basePrice').addEventListener('blur', function() {
        formatPrice(this);
    });
    
    document.getElementById('newPackagePrice').addEventListener('blur', function() {
        formatPrice(this);
    });
    
    document.querySelectorAll('.price-input').forEach(input => {
        input.addEventListener('blur', function() {
            formatPrice(this);
        });
    });
    
    // Modal functions
    function showAddPackageModal() {
        document.getElementById('addPackageModal').classList.remove('hidden');
    }
    
    function closeAddPackageModal() {
        document.getElementById('addPackageModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
