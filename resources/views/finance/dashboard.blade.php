@extends('layouts.app')

@section('page-title', 'Finance Dashboard')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard Finance</h1>
        <p class="text-gray-600 mt-1">Overview keuangan dan approval management</p>
    </div>

    <!-- Period Filter -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-sm p-4">
            <form method="GET" action="{{ route('finance.dashboard') }}" class="flex items-center gap-3">
                <label class="text-sm font-semibold text-gray-700">Periode:</label>
                <select name="period" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Revenue</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
            <p class="text-green-100 text-sm">Total Pendapatan</p>
        </div>

        <!-- Total Expenses -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Expenses</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</h3>
            <p class="text-red-100 text-sm">Total Pengeluaran</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Profit</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">Rp {{ number_format($netProfit, 0, ',', '.') }}</h3>
            <p class="text-blue-100 text-sm">Laba Bersih</p>
        </div>

        <!-- Pending Actions -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <span class="text-sm font-medium bg-white/20 px-3 py-1 rounded-full">Pending</span>
            </div>
            <h3 class="text-3xl font-bold mb-1">{{ $pendingExpenses }}</h3>
            <p class="text-yellow-100 text-sm">Expense Perlu Approval</p>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Pending Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Revenue</p>
                    <h4 class="text-2xl font-bold text-gray-900">Rp {{ number_format($pendingRevenue, 0, ',', '.') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">Order approved belum dibayar</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Payments</p>
                    <h4 class="text-2xl font-bold text-gray-900">Rp {{ number_format($pendingPayments, 0, ',', '.') }}</h4>
                    <p class="text-xs text-gray-500 mt-1">{{ $pendingPaymentCount }} payment request menunggu</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Expenses (Pending Approval) -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Expense Perlu Approval</h3>
                    <a href="{{ route('finance.expenses.index') }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentExpenses as $expense)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $expense->expense_type }}</h4>
                                <p class="text-sm text-gray-600">{{ $expense->project->project_name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $expense->expense_date->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">Rp {{ number_format($expense->amount, 0, ',', '.') }}</p>
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full mt-1">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            </div>
                        </div>
                        @if($expense->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $expense->description }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada expense yang perlu diapprove</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Payment Requests -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Payment Request Approved</h3>
                    <a href="{{ route('finance.payment-requests.index', ['status' => 'approved']) }}" class="text-sm text-purple-600 hover:text-purple-700 font-semibold">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentPaymentRequests as $request)
                    <div class="p-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $request->user->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    @if($request->project)
                                        {{ $request->project->project_name }}
                                    @elseif($request->clas)
                                        {{ $request->clas->name }} (Kelas)
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $request->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-gray-900">Rp {{ number_format($request->approved_amount, 0, ',', '.') }}</p>
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full mt-1">
                                    <i class="fas fa-check-circle mr-1"></i>Approved
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                        <p>Tidak ada payment request</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
