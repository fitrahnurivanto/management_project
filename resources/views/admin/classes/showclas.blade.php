@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelas Berjalan</h1>
                <p class="text-gray-600">Daftar kelas yang sedang aktif/approved</p>
            </div>
            <a href="{{ route('admin.classes.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Semua Kelas
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('admin.classes.showclas') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-filter text-gray-500"></i>
                <span class="text-sm font-semibold text-gray-700">Filter:</span>
            </div>
            
            <!-- Filter Buttons -->
            <a href="{{ route('admin.classes.showclas') }}" 
               class="px-4 py-2 rounded-lg transition {{ !request('kategori') ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <i class="fas fa-th mr-1"></i> Semua
            </a>
            
            @foreach($categories as $category)
                <a href="{{ route('admin.classes.showclas', ['kategori' => $category->id]) }}" 
                   class="px-4 py-2 rounded-lg transition {{ request('kategori') == $category->id ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    @php
                        $kategoriLower = strtolower($category->nama_kategori);
                    @endphp
                    @if(str_contains($kategoriLower, 'private'))
                        <i class="fas fa-user mr-1"></i>
                    @elseif(str_contains($kategoriLower, 'reguler'))
                        <i class="fas fa-users mr-1"></i>
                    @elseif(str_contains($kategoriLower, 'corporate'))
                        <i class="fas fa-building mr-1"></i>
                    @else
                        <i class="fas fa-circle mr-1"></i>
                    @endif
                    {{ $category->nama_kategori }}
                </a>
            @endforeach
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($approvedClasses as $class)
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all border border-gray-100">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 line-clamp-2">{{ $class->name }}</h3>
                            @if($class->instansi)
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-building mr-1"></i>{{ $class->instansi }}</p>
                            @endif
                        </div>
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold whitespace-nowrap ml-2">
                            <i class="fas fa-check-circle mr-1"></i>Aktif
                        </span>
                    </div>

                    <!-- Kategori -->
                    @if($class->kategori)
                    <div class="mb-3">
                        @php
                            $kategoriLower = strtolower($class->kategori->nama_kategori);
                        @endphp
                        @if(str_contains($kategoriLower, 'private'))
                            <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 rounded-lg text-xs font-semibold">
                                <i class="fas fa-user mr-1"></i>{{ $class->kategori->nama_kategori }}
                            </span>
                        @elseif(str_contains($kategoriLower, 'reguler'))
                            <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-xs font-semibold">
                                <i class="fas fa-users mr-1"></i>{{ $class->kategori->nama_kategori }}
                            </span>
                        @elseif(str_contains($kategoriLower, 'corporate'))
                            <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-800 rounded-lg text-xs font-semibold">
                                <i class="fas fa-building mr-1"></i>{{ $class->kategori->nama_kategori }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-semibold">
                                <i class="fas fa-tag mr-1"></i>{{ $class->kategori->nama_kategori }}
                            </span>
                        @endif
                    </div>
                    @endif

                    <!-- Trainer -->
                    <div class="mb-3">
                        <div class="flex flex-wrap gap-1">
                            @if(is_array($class->trainer))
                                @foreach($class->trainer as $trainer)
                                    <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                        <i class="fas fa-user-tie mr-1"></i>{{ $trainer }}
                                    </span>
                                @endforeach
                            @else
                                <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                                    <i class="fas fa-user-tie mr-1"></i>{{ $class->trainer }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Jadwal -->
                    <div class="mb-3 text-sm text-gray-600">
                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                        {{ $class->start_date->format('d M Y') }} - {{ $class->end_date->format('d M Y') }}
                    </div>

                    <!-- Summary -->
                    <div class="flex justify-between text-sm text-gray-600 mb-4 pb-4 border-b border-gray-100">
                        <span><i class="fas fa-users mr-1 text-gray-400"></i>{{ $class->amount }} siswa</span>
                        <span><i class="fas fa-clock mr-1 text-gray-400"></i>{{ $class->duration }} menit</span>
                        <span><i class="fas fa-book mr-1 text-gray-400"></i>{{ $class->meet }}x</span>
                    </div>

                    <!-- Metode -->
                    <div class="mb-4">
                        @if($class->method === 'online')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                <i class="fas fa-laptop mr-1"></i>Online
                            </span>
                        @else
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">
                                <i class="fas fa-building mr-1"></i>Offline
                            </span>
                        @endif
                    </div>

                    <!-- Action Button -->
                    <a href="{{ route('admin.classes.show', $class) }}"
                       class="block w-full text-center px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:shadow-md transition">
                        <i class="fas fa-eye mr-2"></i>Detail Kelas
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <i class="fas fa-chalkboard-teacher text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg mb-4">Belum ada kelas yang aktif</p>
                    <p class="text-gray-400 text-sm">Approve kelas dari halaman manajemen kelas untuk menampilkannya di sini</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
