@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Permintaan Pembayaran</h1>
            <p class="text-gray-600">Kelola payment request dari karyawan</p>
        </div>
        @if($pendingCount > 0)
            <div class="flex items-center gap-2 bg-yellow-50 border border-yellow-200 px-4 py-2 rounded-lg">
                <i class="fas fa-clock text-yellow-600"></i>
                <span class="text-yellow-800 font-semibold">{{ $pendingCount }} Pending Review</span>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-700 text-sm font-medium">Pending</p>
                    <h3 class="text-2xl font-bold text-yellow-900 mt-1">{{ $stats['pending'] ?? 0 }}</h3>
                </div>
                <div class="bg-yellow-200 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-700 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 text-sm font-medium">Disetujui</p>
                    <h3 class="text-2xl font-bold text-green-900 mt-1">{{ $stats['approved'] ?? 0 }}</h3>
                </div>
                <div class="bg-green-200 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-green-700 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-700 text-sm font-medium">Ditolak</p>
                    <h3 class="text-2xl font-bold text-red-900 mt-1">{{ $stats['rejected'] ?? 0 }}</h3>
                </div>
                <div class="bg-red-200 p-3 rounded-lg">
                    <i class="fas fa-times-circle text-red-700 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-700 text-sm font-medium">Total Disetujui</p>
                    <h3 class="text-lg font-bold text-blue-900 mt-1">Rp {{ number_format($stats['total_approved'] ?? 0, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-blue-200 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-blue-700 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm mb-6 p-4">
        <form method="GET" class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Status:</label>
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            @if(request('status'))
                <a href="{{ route('admin.payment-requests.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-times mr-1"></i>Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Diajukan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Disetujui</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                    {{ $request->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $request->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $request->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $request->project->project_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $request->project->project_code }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($request->requested_amount, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($request->approved_amount)
                                    <span class="text-sm font-semibold text-green-600">
                                        Rp {{ number_format($request->approved_amount, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full flex items-center gap-1 w-fit">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @elseif($request->status === 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full flex items-center gap-1 w-fit">
                                        <i class="fas fa-check"></i> Disetujui
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full flex items-center gap-1 w-fit">
                                        <i class="fas fa-times"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.payment-requests.show', $request) }}" 
                                   class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">Belum ada permintaan pembayaran</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
