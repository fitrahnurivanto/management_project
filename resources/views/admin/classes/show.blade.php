@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.classes.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $clas->name }}</h1>
                        <p class="text-gray-600">Detail kelas</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.classes.edit', $clas) }}" 
                       class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('admin.classes.destroy', $clas) }}" 
                          method="POST" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                            <i class="fas fa-trash mr-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informasi Umum -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Umum</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Nama Kelas</p>
                            <p class="font-medium text-gray-900">{{ $clas->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Trainer</p>
                            <p class="font-medium text-gray-900">{{ $clas->trainer }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Metode</p>
                            <p class="font-medium text-gray-900">
                                @if($clas->method === 'online')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                        <i class="fas fa-laptop mr-1"></i> Online
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                        <i class="fas fa-building mr-1"></i> Offline
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="font-medium text-gray-900">
                                @if($clas->status === 'approved')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                        <i class="fas fa-check-circle mr-1"></i> Approved
                                    </span>
                                @elseif($clas->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                        <i class="fas fa-times-circle mr-1"></i> Rejected
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Jadwal -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Jadwal</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Mulai</p>
                            <p class="font-medium text-gray-900">{{ $clas->start_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Selesai</p>
                            <p class="font-medium text-gray-900">{{ $clas->end_date->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jumlah Pertemuan</p>
                            <p class="font-medium text-gray-900">{{ $clas->meet }}x</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Durasi per Pertemuan</p>
                            <p class="font-medium text-gray-900">{{ $clas->duration }} menit</p>
                        </div>
                    </div>
                </div>

                <!-- Deskripsi -->
                @if($clas->description)
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Deskripsi</h2>
                    <p class="text-gray-700">{{ $clas->description }}</p>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Finansial -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Finansial</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-sm text-gray-600">Harga Kelas</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($clas->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b">
                            <span class="text-sm text-gray-600">Biaya Operasional</span>
                            <span class="font-medium text-red-600">Rp {{ number_format($clas->cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-semibold text-gray-900">Income Bersih</span>
                            <span class="font-bold text-green-600 text-lg">Rp {{ number_format($clas->income, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Metadata</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Slug</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $clas->slug }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Dibuat</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $clas->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Terakhir Update</p>
                            <p class="font-medium text-gray-900 text-sm">{{ $clas->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
