@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Karyawan</h1>
            <p class="text-gray-600">Kelola data karyawan dan employee</p>
        </div>
        <a href="{{ route('admin.karyawan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-[#7b2cbf] to-[#9d4edd] text-white rounded-xl shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all text-sm font-medium">
            <i class="fas fa-user-plus"></i>
            Tambah Karyawan
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4 mb-6">
        <form action="{{ route('admin.karyawan.index') }}" method="GET" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama atau email karyawan..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
            </div>
            <button type="submit" class="px-6 py-2 bg-[#7b2cbf] text-white rounded-lg hover:bg-[#6a25a8] transition">
                <i class="fas fa-search mr-2"></i> Cari
            </button>
            @if(request('search'))
            <a href="{{ route('admin.karyawan.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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

    <!-- Karyawan List -->
    @if($karyawan->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-user-tie text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Belum Ada Karyawan</h3>
        <p class="text-gray-500 mb-6">Mulai tambahkan karyawan untuk mengelola project</p>
        <a href="{{ route('admin.karyawan.create') }}" class="inline-block bg-[#7b2cbf] text-white px-6 py-3 rounded-lg hover:bg-[#6a25a8] transition">
            <i class="fas fa-user-plus mr-2"></i> Tambah Karyawan Pertama
        </a>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Projects</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bergabung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($karyawan as $employee)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            @if($employee->avatar)
                            <img src="{{ Storage::url($employee->avatar) }}" 
                                 alt="{{ $employee->name }}" 
                                 class="w-12 h-12 rounded-full object-cover shadow-md border-2 border-white ring-2 ring-purple-200">
                            @else
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                                {{ strtoupper(substr($employee->name, 0, 2)) }}
                            </div>
                            @endif
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $employee->name }}</div>
                                <div class="text-xs text-gray-500">Employee</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @if($employee->address)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 font-medium">
                                    <i class="fas fa-briefcase mr-1.5 text-xs"></i>
                                    {{ $employee->address }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs italic">Belum diset</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 flex items-center gap-2">
                            <i class="fas fa-envelope text-gray-400 text-xs"></i>
                            {{ $employee->email }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-briefcase mr-1"></i>
                            {{ $employee->projects_count ?? 0 }} Project{{ ($employee->projects_count ?? 0) != 1 ? 's' : '' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($employee->has_active_projects)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <i class="fas fa-circle text-xs mr-1"></i>
                            Active
                        </span>
                        @elseif($employee->projects_count > 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-pause-circle text-xs mr-1"></i>
                            Standby
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                            <i class="fas fa-clock text-xs mr-1"></i>
                            Available
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $employee->created_at->format('d M Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $employee->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.karyawan.show', $employee) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium"
                               title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.karyawan.edit', $employee) }}" 
                               class="text-green-600 hover:text-green-800 font-medium"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.karyawan.destroy', $employee) }}" method="POST" 
                                  onsubmit="return confirm('Yakin ingin menghapus karyawan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-800 font-medium"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Pagination -->
    @if($karyawan->hasPages())
    <div class="mt-6">
        {{ $karyawan->links() }}
    </div>
    @endif
</div>
@endsection
