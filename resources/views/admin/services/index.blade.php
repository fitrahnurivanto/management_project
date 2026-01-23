@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Layanan & Paket</h1>
            <p class="text-gray-600">Edit nama layanan, harga, dan paket-paket yang tersedia</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-list mr-2"></i>Lihat Orders
            </a>
            <a href="{{ route('admin.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Create Order
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Services List Grouped by Category -->
    @foreach($services as $categoryName => $categoryServices)
    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-folder-open text-indigo-600 mr-2"></i>
            {{ $categoryName }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($categoryServices as $service)
            <div class="bg-white rounded-xl shadow-md border border-gray-200 hover:shadow-lg transition">
                <div class="p-5">
                    <!-- Service Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg">{{ $service->name }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ $service->description ?? 'Tidak ada deskripsi' }}</p>
                        </div>
                        @if(!$service->is_active)
                        <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-600 rounded-full">Nonaktif</span>
                        @endif
                    </div>
                    
                    <!-- Base Price -->
                    <div class="bg-indigo-50 rounded-lg p-3 mb-3">
                        <div class="text-xs text-gray-600 mb-1">Harga Dasar</div>
                        <div class="text-xl font-bold text-indigo-600">
                            Rp {{ number_format($service->base_price, 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <!-- Packages Count -->
                    <div class="mb-3">
                        <div class="text-sm text-gray-600">
                            <i class="fas fa-box mr-1"></i>
                            <span class="font-semibold">{{ $service->packages->count() }}</span> Paket tersedia
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <a href="{{ route('admin.services.edit', $service) }}" 
                       class="block w-full text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2 rounded-lg hover:shadow-lg transition font-semibold">
                        <i class="fas fa-edit mr-2"></i>Edit Layanan & Paket
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
