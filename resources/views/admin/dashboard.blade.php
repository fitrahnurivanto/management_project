@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard ')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        height: 600px;
    }
    
    .fc-event {
        cursor: pointer;
    }
    
    .fc-event:hover {
        opacity: 0.8;
    }
    
    .calendar-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .calendar-content.active {
        max-height: 700px;
        overflow-y: auto;
    }
</style>
@endpush

@section('content')
<!-- Division Switcher for Super Admin -->
@if($user->isSuperAdmin())
<div class="mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fas fa-building text-2xl text-indigo-600"></i>
                <div>
                    <h5 class="text-lg font-semibold text-gray-800">Divisi</h5>
                    <p class="text-sm text-gray-500">Pilih divisi untuk melihat data</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.dashboard', array_merge(request()->except('division'), ['division' => 'agency'])) }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ $activeDivision === 'agency' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-briefcase mr-2"></i>Agency
                </a>
                <a href="{{ route('admin.dashboard', array_merge(request()->except('division'), ['division' => 'academy'])) }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ $activeDivision === 'academy' ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    <i class="fas fa-graduation-cap mr-2"></i>Academy
                </a>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Filter Form -->
<div class="flex justify-between items-center mb-6">
    <h4 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-chart-line mr-3"></i>Dashboard 
        @if($user->isAgencyAdmin())
            <span class="text-indigo-600">Agency</span>
        @elseif($user->isAcademyAdmin())
            <span class="text-green-600">Academy</span>
        @elseif($user->isSuperAdmin())
            <span class="{{ $activeDivision === 'agency' ? 'text-indigo-600' : 'text-green-600' }}">
                {{ ucfirst($activeDivision) }}
            </span>
        @endif
    </h4>
    
    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex gap-3">
        @if($user->isSuperAdmin())
            <input type="hidden" name="division" value="{{ request('division', $activeDivision) }}">
        @endif
        
        <select name="period" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" onchange="this.form.submit()">
            <option value="all" {{ $period == 'all' ? 'selected' : '' }}>Semua Periode</option>
            <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
            <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
            <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
            <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
        </select>
        
        <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" onchange="this.form.submit()">
            <option value="all" {{ request('year', 'all') == 'all' ? 'selected' : '' }}>Semua Tahun</option>
            @for($y = now()->year; $y >= 2023; $y--)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white" onchange="this.form.submit()">
            <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Selesai</option>
            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Aktif</option>
        </select>
    </form>
</div>
</div>
<!-- Stats Cards -->
@if($activeDivision === 'academy')
<!-- Academy Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Class Income -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Pendapatan Kelas</p>
                    <h4 class="text-2xl font-bold text-green-600">Rp {{ number_format($totalClassIncome, 0, ',', '.') }}</h4>
                    <p class="text-gray-400 text-xs mt-1">Semua kelas approved</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-graduation-cap text-3xl text-green-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtered Class Revenue -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Pendapatan (Filtered)</p>
                    <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($classRevenue, 0, ',', '.') }}</h4>
                    <p class="text-gray-400 text-xs mt-1">Sesuai filter periode</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-filter text-3xl text-blue-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Classes -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Kelas Berjalan</p>
                    <h4 class="text-2xl font-bold text-gray-800">{{ $ongoingClasses->count() }}</h4>
                    <p class="text-gray-400 text-xs mt-1">Sedang berlangsung</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-xl">
                    <i class="fas fa-chalkboard-teacher text-3xl text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Income per Class -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Rata-rata per Kelas</p>
                    @php
                        $totalClasses = \App\Models\Clas::where('status', 'approved')->count();
                        $avgIncome = $totalClasses > 0 ? $totalClassIncome / $totalClasses : 0;
                    @endphp
                    <h4 class="text-2xl font-bold text-gray-800">Rp {{ number_format($avgIncome, 0, ',', '.') }}</h4>
                    <p class="text-gray-400 text-xs mt-1">{{ $totalClasses }} kelas total</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-chart-pie text-3xl text-indigo-500"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Class Revenue Chart -->
<div class="bg-white rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-chart-line mr-2 text-green-600"></i>Pendapatan Kelas per Bulan ({{ $selectedYear }})
        </h5>
    </div>
    <div class="p-6">
        <div class="chart-container">
            <canvas id="classRevenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Ongoing Classes List -->
@if($ongoingClasses->count() > 0)
<div class="bg-white rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800">
            <i class="fas fa-play-circle mr-2 text-green-600"></i>Kelas Sedang Berjalan
        </h5>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ongoingClasses as $class)
            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">
                <h6 class="font-semibold text-gray-900 mb-2">{{ $class->name }}</h6>
                <div class="space-y-1 text-sm">
                    <p class="text-gray-600"><i class="fas fa-building text-gray-400 w-4"></i> {{ $class->instansi ?? '-' }}</p>
                    <p class="text-gray-600"><i class="fas fa-users text-gray-400 w-4"></i> {{ $class->amount }} peserta</p>
                    <p class="text-green-600 font-semibold"><i class="fas fa-money-bill text-gray-400 w-4"></i> Rp {{ number_format($class->income, 0, ',', '.') }}</p>
                    <p class="text-gray-500 text-xs">
                        <i class="fas fa-calendar text-gray-400 w-4"></i> 
                        {{ $class->start_date->format('d/m/Y') }} - {{ $class->end_date->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@else
<!-- Agency Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Revenue Card -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Revenue</p>
                    <h4 class="text-2xl font-bold text-gray-800" data-stat="totalRevenue">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h4>
                    <p class="{{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1" data-growth="revenueGrowth">
                        @if($revenueGrowth != 0)
                            <i class="fas fa-arrow-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}"></i> 
                            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                        @endif
                    </p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-money-bill-wave text-3xl text-green-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Cost Card -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Cost</p>
                    <h4 class="text-2xl font-bold text-gray-800" data-stat="totalCost">Rp {{ number_format($totalCost, 0, ',', '.') }}</h4>
                    <p class="{{ $costGrowth <= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1" data-growth="costGrowth">
                        @if($costGrowth != 0)
                            <i class="fas fa-arrow-{{ $costGrowth >= 0 ? 'up' : 'down' }}"></i> 
                            {{ $costGrowth >= 0 ? '+' : '' }}{{ number_format($costGrowth, 1) }}%
                        @endif
                    </p>
                </div>
                <div class="bg-red-100 p-3 rounded-xl">
                    <i class="fas fa-receipt text-3xl text-red-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Profit Card -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Total Profit</p>
                    <h4 class="{{ $totalProfit >= 0 ? 'text-green-500' : 'text-red-500' }} text-2xl font-bold" data-stat="totalProfit">
                        Rp {{ number_format($totalProfit, 0, ',', '.') }}
                    </h4>
                    <p class="{{ $profitGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1" data-growth="profitGrowth">
                        @if($profitGrowth != 0)
                            <i class="fas fa-arrow-{{ $profitGrowth >= 0 ? 'up' : 'down' }}"></i> 
                            {{ $profitGrowth >= 0 ? '+' : '' }}{{ number_format($profitGrowth, 1) }}%
                        @endif
                    </p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-chart-line text-3xl text-indigo-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Margin Card -->
    <div class="bg-white rounded-2xl shadow-sm hover:-translate-y-1 transition-transform duration-300">
        <div class="p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm mb-1">Profit Margin</p>
                    <h4 class="text-2xl font-bold text-gray-800" data-stat="profitMargin">{{ number_format($profitMargin, 1) }}%</h4>
                    <p class="{{ abs($marginChange) > 0.1 ? ($marginChange >= 0 ? 'text-green-500' : 'text-red-500') : 'text-blue-500' }} text-sm mt-1" data-growth="marginChange">
                        @if(abs($marginChange) > 0.1)
                            <i class="fas fa-arrow-{{ $marginChange >= 0 ? 'up' : 'down' }}"></i> 
                            {{ $marginChange >= 0 ? '+' : '' }}{{ number_format($marginChange, 1) }}%
                        @else
                            <i class="fas fa-minus"></i> Stabil
                        @endif
                    </p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-xl">
                    <i class="fas fa-percentage text-3xl text-yellow-500"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects Stats (Agency Only) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-6 text-center">
            <i class="fas fa-project-diagram text-5xl text-indigo-500 mb-3"></i>
            <h4 class="text-3xl font-bold text-gray-800" data-stat="totalProjects">{{ $totalProjects }}</h4>
            <p class="text-gray-500 mt-2">Total Projects</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-6 text-center">
            <i class="fas fa-check-circle text-5xl text-green-500 mb-3"></i>
            <h4 class="text-3xl font-bold text-gray-800" data-stat="completedProjects">{{ $completedProjects }}</h4>
            <p class="text-gray-500 mt-2">Completed Projects</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm">
        <div class="p-6 text-center">
            <i class="fas fa-spinner text-5xl text-yellow-500 mb-3"></i>
            <h4 class="text-3xl font-bold text-gray-800" data-stat="activeProjects">{{ $activeProjects }}</h4>
            <p class="text-gray-500 mt-2">Active Projects</p>
        </div>
    </div>
</div>
@endif

<!-- Revenue & Outstanding Charts Row (Agency Only) -->
@if($activeDivision === 'agency')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Monthly Revenue Trend (Bar Chart) -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-chart-bar mr-2"></i>Total Pendapatan (12 Bulan Terakhir)</h5>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Outstanding Payments (Bar Chart) -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-exclamation-triangle mr-2"></i>Pembayaran Belum Lunas</h5>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="outstandingChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Category, Projects & Comparison Row (Agency Only) -->
@if($activeDivision === 'agency')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Revenue by Service (Doughnut Chart) -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-chart-pie mr-2"></i>Revenue per Layanan</h5>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="serviceChart"></canvas>
            </div>
            @if($revenueByService->count() == 0)
                <p class="text-gray-500 text-center py-8">Belum ada data</p>
            @endif
        </div>
    </div>

    <!-- Top Profitable Projects (Bar Chart) -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-trophy mr-2"></i>Top 5 Profitable Projects</h5>
        </div>
        <div class="p-6">
            @if($topProjects->count() > 0)
                <div class="chart-container">
                    <canvas id="projectsChart"></canvas>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada data project</p>
            @endif
        </div>
    </div>

    <!-- Revenue vs Cost Comparison -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-balance-scale mr-2"></i>Revenue vs Cost</h5>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Target vs Actual -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div id="target-omset" class="bg-white rounded-2xl shadow-sm scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-bullseye mr-2"></i>Target Omset Bulanan</h5>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ \Carbon\Carbon::create(request('target_year', now()->year), request('target_month', now()->month), 1)->format('F Y') }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Month Navigation -->
                    <div class="flex items-center gap-2">
                        <form method="GET" action="{{ route('admin.dashboard') }}#target-omset" class="inline">
                            @foreach(request()->except(['target_month', 'target_year']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="target_month" value="{{ request('target_month', now()->month) == 1 ? 12 : request('target_month', now()->month) - 1 }}">
                            <input type="hidden" name="target_year" value="{{ request('target_month', now()->month) == 1 ? request('target_year', now()->year) - 1 : request('target_year', now()->year) }}">
                            <button type="submit" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </form>
                        
                        <form method="GET" action="{{ route('admin.dashboard') }}#target-omset" class="inline">
                            @foreach(request()->except(['target_month', 'target_year']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <input type="hidden" name="target_month" value="{{ request('target_month', now()->month) == 12 ? 1 : request('target_month', now()->month) + 1 }}">
                            <input type="hidden" name="target_year" value="{{ request('target_month', now()->month) == 12 ? request('target_year', now()->year) + 1 : request('target_year', now()->year) }}">
                            <button type="submit" class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-gray-100 rounded-lg transition">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-right">
                        @if($selectedTargetAmount > 0)
                            <p class="text-sm text-gray-500">Target</p>
                            <p class="text-xl font-bold text-indigo-600">Rp {{ number_format($selectedTargetAmount, 0, ',', '.') }}</p>
                            <button onclick="openTargetModal()" class="mt-2 text-xs text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-edit mr-1"></i>Edit Target
                            </button>
                        @else
                            <button onclick="openTargetModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">
                                <i class="fas fa-plus mr-1"></i>Set Target
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="weeklyTargetChart"></canvas>
            </div>
            @if($selectedTargetAmount == 0)
                <p class="text-gray-500 text-center py-8">Silakan set target omset bulanan terlebih dahulu</p>
            @endif
        </div>
    </div>
    
    <!-- Placeholder for future chart -->
    <div class="bg-white rounded-2xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-chart-area mr-2"></i>Available Space</h5>
        </div>
        <div class="p-6">
            <p class="text-gray-500 text-center py-8">Area untuk grafik tambahan</p>
        </div>
    </div>
</div>
@endif

<!-- Ongoing Classes (Kelas Berjalan) - Only for Academy -->
@if($activeDivision === 'academy')
<div class="bg-white rounded-xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-chalkboard-teacher mr-2 text-green-600"></i>Kelas Berjalan</h5>
            <p class="text-sm text-gray-600">Kelas yang sedang aktif berlangsung</p>
        </div>
        <div class="flex items-center gap-3">
            @if($ongoingClasses->count() > 0)
            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">
                {{ $ongoingClasses->count() }} Kelas Aktif
            </span>
            @else
            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-sm font-semibold rounded-full">
                Tidak ada kelas aktif
            </span>
            @endif
            <a href="{{ route('admin.classes.index') }}" class="text-green-600 hover:text-green-700 font-medium text-sm">
                <i class="fas fa-list mr-1"></i>Lihat Semua Kelas
            </a>
        </div>
    </div>
    @if($ongoingClasses->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nama Kelas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Instansi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Pertemuan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Income</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($ongoingClasses as $class)
                @php
                    $totalDays = $class->start_date->diffInDays($class->end_date) + 1;
                    $daysElapsed = $class->start_date->diffInDays(now()) + 1;
                    $progressPercent = min(100, round(($daysElapsed / $totalDays) * 100));
                @endphp
                <tr class="hover:bg-green-50 transition">
                    <td class="px-6 py-4">
                        <div>
                            <a href="{{ route('admin.classes.show', $class) }}" class="font-semibold text-gray-900 hover:text-green-600">{{ $class->name }}</a>
                            @if($class->description)
                            <div class="text-sm text-gray-600 mt-1">{{ Str::limit($class->description, 40) }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $class->instansi ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div class="text-gray-900 font-medium">{{ $class->start_date->format('d M Y') }}</div>
                            <div class="text-gray-600">s/d {{ $class->end_date->format('d M Y') }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-users text-gray-500"></i>
                            <span class="font-semibold text-gray-900">{{ $class->amount }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check text-gray-500"></i>
                            <span class="font-semibold text-gray-900">{{ $class->meet }}x</span>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">{{ $class->duration }} menit</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($class->method == 'online')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-laptop mr-1"></i>Online
                            </span>
                        @else
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-building mr-1"></i>Offline
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-green-600">
                            Rp {{ number_format($class->income, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="w-32">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-semibold text-gray-700">{{ $progressPercent }}%</span>
                                <span class="text-xs text-gray-600">{{ $daysElapsed }}/{{ $totalDays }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="p-8 text-center">
        <i class="fas fa-chalkboard-teacher text-6xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg mb-2">Tidak ada kelas yang sedang berjalan saat ini</p>
        <p class="text-gray-400 text-sm mb-4">Kelas berjalan adalah kelas dengan status "approved" dan berada dalam periode aktif (antara start_date dan end_date)</p>
        <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-list mr-2"></i>Lihat Semua Kelas
        </a>
    </div>
    @endif
</div>
@endif

<!-- Project Calendar (Collapsible) -->
<div class="bg-white rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer hover:bg-gray-50" onclick="toggleCalendar()">
        <div>
            <h5 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-calendar-alt mr-2"></i>Project Calendar
            </h5>
            <p class="text-sm text-gray-500 mt-1">Timeline view of all projects - Click to expand</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex gap-2" id="calendarFilters" style="display: none;">
                <button onclick="event.stopPropagation(); filterByStatus('all')" class="px-3 py-1.5 text-xs rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                    All
                </button>
                <button onclick="event.stopPropagation(); filterByStatus('in_progress')" class="px-3 py-1.5 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors">
                    In Progress
                </button>
                <button onclick="event.stopPropagation(); filterByStatus('completed')" class="px-3 py-1.5 text-xs rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition-colors">
                    Completed
                </button>
                <button onclick="event.stopPropagation(); filterByStatus('pending')" class="px-3 py-1.5 text-xs rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200 transition-colors">
                    Pending
                </button>
            </div>
            <i class="fas fa-chevron-down text-gray-400 transition-transform duration-300" id="calendarChevron"></i>
        </div>
    </div>
    <div class="calendar-content" id="calendarContent">
        <div class="p-6">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="bg-white rounded-2xl shadow-sm mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h5 class="text-lg font-semibold text-gray-800"><i class="fas fa-history mr-2"></i>Recent Activities</h5>
    </div>
    <div class="p-6">
        <div data-activities="container">
            @if($recentActivities->count() > 0)
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                        <div class="flex justify-between items-start py-3 border-b border-gray-100 last:border-0">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle text-indigo-500 text-xs mt-2"></i>
                                <div>
                                    <p class="text-gray-800">
                                        <strong>{{ $activity->user ? $activity->user->name : 'System' }}</strong>
                                        <span class="text-gray-600">- {{ $activity->description }}</span>
                                    </p>
                                </div>
                            </div>
                            <small class="text-gray-500 text-sm whitespace-nowrap ml-4">{{ $activity->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Belum ada aktivitas</p>
            @endif
        </div>
    </div>
</div>

<!-- Modal Set/Edit Target -->
<div id="targetModal" class="hidden fixed inset-0 bg-transparent z-50 flex items-center justify-center" style="backdrop-filter: blur(3px);">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-bullseye mr-2"></i>Set Target Omset Bulanan
            </h3>
            <button onclick="closeTargetModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.dashboard.save-target') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-4">
                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan & Tahun</label>
                    <div class="grid grid-cols-2 gap-3">
                        <select name="month" required class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" required class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            @for($y = now()->year - 1; $y <= now()->year + 2; $y++)
                                <option value="{{ $y }}" {{ now()->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Division (hidden for agency/academy admin) -->
                @if($user->isSuperAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Divisi</label>
                    <select name="division" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="agency" {{ $activeDivision == 'agency' ? 'selected' : '' }}>Agency</option>
                        <option value="academy" {{ $activeDivision == 'academy' ? 'selected' : '' }}>Academy</option>
                    </select>
                </div>
                @else
                <input type="hidden" name="division" value="{{ $user->division }}">
                @endif

                <!-- Target Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Omset (Rp)</label>
                    <input type="text" name="target_amount_display" id="target_amount_display" required
                           placeholder="Contoh: 50.000.000" 
                           value="{{ $targetAmount > 0 ? number_format($targetAmount, 0, ',', '.') : '' }}"
                           oninput="formatCurrencyDashboard(this)"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <input type="hidden" name="target_amount" id="target_amount" value="{{ $targetAmount > 0 ? $targetAmount : '' }}">
                    <p class="text-xs text-gray-500 mt-1">Gunakan format ribuan dengan titik (contoh: 50.000.000)</p>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Optional)</label>
                    <textarea name="notes" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Target untuk..."></textarea>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeTargetModal()" 
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-save mr-2"></i>Simpan Target
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Modal functions
    function openTargetModal() {
        document.getElementById('targetModal').classList.remove('hidden');
    }
    
    function closeTargetModal() {
        document.getElementById('targetModal').classList.add('hidden');
    }

    // Format currency untuk dashboard
    function formatCurrencyDashboard(input) {
        let value = input.value.replace(/\D/g, '');
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(value);
            document.getElementById('target_amount').value = value;
        } else {
            input.value = '';
            document.getElementById('target_amount').value = '';
        }
    }

    // ========================================
    // CHART INITIALIZATION (NO AJAX)
    // ========================================
    
    // Chart.js default config
    Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
    Chart.defaults.color = '#6b7280';

    let dashboardCharts = {}; // Store chart instances

    // 1. Monthly Revenue Trend (Bar Chart)
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = {!! json_encode($monthlyRevenue) !!};
        
        dashboardCharts.revenue = new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: revenueData.map(item => item.month),
                datasets: [{
                    label: 'Total Pendapatan',
                    data: revenueData.map(item => item.total),
                    backgroundColor: 'rgba(79, 70, 229, 0.8)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2,
                    borderRadius: 1,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                    maxBarThickness: 80
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = revenueData[context.dataIndex];
                                const amount = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                const projects = item.project_count + ' project' + (item.project_count > 1 ? 's' : '');
                                return [amount, projects];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        offset: true,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // 2. Outstanding Payments (Bar Chart)
    const outstandingCtx = document.getElementById('outstandingChart');
    if (outstandingCtx) {
        const outstandingData = {!! json_encode($outstandingPayments) !!};
        
        dashboardCharts.outstanding = new Chart(outstandingCtx, {
            type: 'bar',
            data: {
                labels: outstandingData.map(item => item.month),
                datasets: [{
                    label: 'Belum Lunas',
                    data: outstandingData.map(item => item.total),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    borderRadius: 1,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.6,
                    maxBarThickness: 80
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = outstandingData[context.dataIndex];
                                const amount = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                const projects = item.project_count + ' project' + (item.project_count > 1 ? 's' : '');
                                return [amount, projects];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        offset: true,
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // 3. Revenue by Service (Doughnut Chart)
    const serviceCtx = document.getElementById('serviceChart');
    if (serviceCtx && {{ $revenueByService->count() > 0 ? 'true' : 'false' }}) {
        const serviceData = {!! json_encode($revenueByService) !!};
        
        dashboardCharts.service = new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: serviceData.map(item => item.name),
                datasets: [{
                    data: serviceData.map(item => item.total),
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(234, 179, 8, 0.8)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const item = serviceData[context.dataIndex];
                                const amount = 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                const projects = item.project_count + ' project' + (item.project_count > 1 ? 's' : '');
                                return [amount, projects];
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Top Profitable Projects (Bar Chart)
    const projectsCtx = document.getElementById('projectsChart');
    if (projectsCtx && {{ $topProjects->count() > 0 ? 'true' : 'false' }}) {
        dashboardCharts.projects = new Chart(projectsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProjects->pluck('title')) !!},
                datasets: [{
                    label: 'Profit',
                    data: {!! json_encode($topProjects->pluck('profit')) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Profit: Rp ' + (context.parsed.y / 1000000).toFixed(1) + 'JT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // 5. Revenue vs Cost Comparison (Bar Chart)
    const comparisonCtx = document.getElementById('comparisonChart');
    if (comparisonCtx) {
        dashboardCharts.comparison = new Chart(comparisonCtx, {
            type: 'bar',
            data: {
                labels: ['Revenue', 'Cost', 'Profit'],
                datasets: [{
                    label: 'Amount',
                    data: [{{ $totalRevenue }}, {{ $totalCost }}, {{ $totalProfit }}],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(102, 126, 234, 0.8)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(102, 126, 234, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': Rp ' + (context.parsed.y / 1000000).toFixed(1) + 'JT';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // 6. Weekly Target vs Actual (Line Chart)
    const weeklyTargetCtx = document.getElementById('weeklyTargetChart');
    if (weeklyTargetCtx && {{ $selectedTargetAmount > 0 ? 'true' : 'false' }}) {
        const weeklyData = {!! json_encode($weeklyData) !!};
        const targetAmount = {{ $selectedTargetAmount }};
        
        new Chart(weeklyTargetCtx, {
            type: 'line',
            data: {
                labels: weeklyData.map(w => w.week),
                datasets: [
                    {
                        label: 'Target per Minggu',
                        data: Array(4).fill(targetAmount / 4),
                        borderColor: 'rgba(239, 68, 68, 1)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        borderDash: [10, 5],
                        fill: false,
                        tension: 0,
                        pointRadius: 0,
                        pointHoverRadius: 0
                    },
                    {
                        label: 'Omset Actual',
                        data: weeklyData.map(w => w.revenue),
                        borderColor: 'rgba(79, 70, 229, 1)',
                        backgroundColor: 'rgba(79, 70, 229, 0.2)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        pointBackgroundColor: 'rgba(79, 70, 229, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const dataIndex = context.dataIndex;
                                const percentage = weeklyData[dataIndex] ? weeklyData[dataIndex].percentage : 0;
                                
                                if (context.datasetIndex === 0) {
                                    return 'Target: Rp ' + (context.parsed.y / 1000000).toFixed(1) + 'JT';
                                } else {
                                    return [
                                        'Actual: Rp ' + (context.parsed.y / 1000000).toFixed(1) + 'JT',
                                        'Progress: ' + percentage + '% dari target'
                                    ];
                                }
                            }
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'JT';
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    }
                }
            }
        });
    }

    // Toggle Calendar Visibility
    function toggleCalendar() {
        const content = document.getElementById('calendarContent');
        const chevron = document.getElementById('calendarChevron');
        const filters = document.getElementById('calendarFilters');
        
        content.classList.toggle('active');
        chevron.style.transform = content.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
        filters.style.display = content.classList.contains('active') ? 'flex' : 'none';
        
        // Initialize calendar on first open
        if (content.classList.contains('active') && !window.calendarInitialized) {
            initializeCalendar();
            window.calendarInitialized = true;
        }
    }

    // FullCalendar Implementation
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        let currentStatusFilter = 'all';
        
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                list: 'List'
            },
            height: 580,
            dayMaxEvents: 3,
            moreLinkClick: 'popover',
            eventClick: function(info) {
                // Redirect to project detail page
                window.location.href = info.event.extendedProps.url;
            },
            eventDidMount: function(info) {
                // Add tooltip with full project details
                const props = info.event.extendedProps;
                let tooltipText = props.client + ' - ' + props.project_name + '\n';
                tooltipText += 'Code: ' + props.project_code + '\n';
                tooltipText += 'Status: ' + props.status.toUpperCase() + '\n';
                if (props.start_date) {
                    tooltipText += 'Start: ' + props.start_date + '\n';
                }
                if (props.end_date) {
                    tooltipText += 'Deadline: ' + props.end_date + '\n';
                }
                if (props.budget) {
                    tooltipText += 'Budget: Rp ' + new Intl.NumberFormat('id-ID').format(props.budget);
                }
                if (props.is_overdue) {
                    tooltipText += '\n⚠ OVERDUE!';
                }
                
                info.el.setAttribute('title', tooltipText);
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                // Fetch events from API
                const division = '{{ $activeDivision }}';
                const url = '{{ route("admin.dashboard.calendar-events") }}?division=' + division;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Apply status filter
                        let filteredData = data;
                        if (currentStatusFilter !== 'all') {
                            filteredData = data.filter(event => event.extendedProps.status === currentStatusFilter);
                        }
                        successCallback(filteredData);
                    })
                    .catch(error => {
                        console.error('Error fetching calendar events:', error);
                        failureCallback(error);
                    });
            }
        });
        
        calendar.render();
        
        // Status filter function
        window.filterByStatus = function(status) {
            currentStatusFilter = status;
            calendar.refetchEvents();
        };
    }

    // ========================================
    // ACADEMY CLASS REVENUE CHART
    // ========================================
    @if($activeDivision === 'academy')
    const classRevenueCtx = document.getElementById('classRevenueChart');
    if (classRevenueCtx) {
        const monthlyClassRevenue = {!! json_encode($monthlyClassRevenue) !!};
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        
        new Chart(classRevenueCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Pendapatan Kelas',
                    data: monthlyClassRevenue,
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    @endif
</script>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
@endpush

@endsection
