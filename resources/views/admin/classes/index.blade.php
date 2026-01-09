@extends('layouts.app')

@section('content')
<style>
    .tooltip {
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%) translateY(-8px);
        padding: 6px 12px;
        background-color: #1f2937;
        color: white;
        font-size: 12px;
        border-radius: 6px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s, transform 0.2s;
        z-index: 50;
    }
    
    .tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 4px solid transparent;
        border-top-color: #1f2937;
    }
    
    .group:hover .tooltip {
        opacity: 1;
        transform: translateX(-50%) translateY(-4px);
    }
</style>

<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Kelas</h1>
            <p class="text-gray-600">Kelola data kelas pelatihan dan sertifikasi</p>
        </div>
        <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#7b2cbf] to-[#9d4edd] text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm font-medium">
            <i class="fas fa-plus"></i>
            Tambah Kelas
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('admin.classes.index') }}" method="GET" class="flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama kelas atau trainer..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
            </div>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Active</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="method" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                <option value="">Semua Metode</option>
                <option value="online" {{ request('method') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ request('method') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
            <button type="submit" class="px-6 py-2 bg-[#7b2cbf] text-white rounded-lg hover:bg-[#6a25a8] transition">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            @if(request('search') || request('status') || request('method'))
            <a href="{{ route('admin.classes.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i> Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Classes List -->
    @if($classes->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-chalkboard-teacher text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-4">Belum ada data kelas</p>
        <a href="{{ route('admin.classes.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-[#7b2cbf] text-white rounded-lg hover:bg-[#6a25a8] transition">
            <i class="fas fa-plus"></i> Tambah Kelas Pertama
        </a>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trainer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($classes as $class)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <!-- Status Actions di kiri -->
                            <div class="flex flex-col gap-1 pt-1">
                                @if($class->status === 'pending')
                                    <form action="{{ route('admin.classes.approve', $class) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="group relative p-1.5 bg-green-50 hover:bg-green-100 text-green-600 hover:text-green-700 rounded transition">
                                            <i class="fas fa-check text-xs"></i>
                                            <span class="tooltip">Approve</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.classes.reject', $class) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="group relative p-1.5 bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 rounded transition">
                                            <i class="fas fa-times text-xs"></i>
                                            <span class="tooltip">Reject</span>
                                        </button>
                                    </form>
                                @elseif($class->status === 'rejected')
                                    <form action="{{ route('admin.classes.approve', $class) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="group relative p-1.5 bg-green-50 hover:bg-green-100 text-green-600 hover:text-green-700 rounded transition">
                                            <i class="fas fa-check text-xs"></i>
                                            <span class="tooltip">Approve</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            <!-- Nama Kelas -->
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $class->name }}</div>
                                <div class="text-sm text-gray-500">{{ $class->meet }}x pertemuan • {{ $class->duration }} menit</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($class->status === 'approved')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Active
                            </span>
                        @elseif($class->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Rejected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $class->trainer }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($class->method === 'online')
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-laptop mr-1"></i> Online
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-building mr-1"></i> Offline
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $class->start_date->format('d M Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $class->end_date->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.classes.show', $class) }}" 
                               class="group relative p-2 bg-blue-50 hover:bg-blue-100 text-blue-600 hover:text-blue-700 rounded-lg transition">
                                <i class="fas fa-eye text-sm"></i>
                                <span class="tooltip">Lihat Detail</span>
                            </a>
                            <a href="{{ route('admin.classes.edit', $class) }}" 
                               class="group relative p-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-600 hover:text-yellow-700 rounded-lg transition">
                                <i class="fas fa-edit text-sm"></i>
                                <span class="tooltip">Edit</span>
                            </a>
                            <form action="{{ route('admin.classes.destroy', $class) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?');"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="group relative p-2 bg-red-100 hover:bg-red-200 text-red-700 hover:text-red-800 rounded-lg transition">
                                    <i class="fas fa-trash text-sm"></i>
                                    <span class="tooltip">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $classes->links() }}
    </div>
    @endif
</div>
@endsection
