@extends('layouts.app')

@section('title', 'Employee Dashboard')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 350px;
    }
</style>
@endpush

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h2>
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Active Projects</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active_projects'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-project-diagram text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Pending Tasks</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $stats['pending_tasks'] }}</h3>
                </div>
                <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-2xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hours This Month</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['hours_this_month'], 1) }}</h3>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Sudah Cair - Bulan Ini</p>
                <i class="fas fa-money-bill-wave text-xl opacity-75"></i>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($paymentStats['total_this_month'], 0, ',', '.') }}</h3>
            <p class="text-xs opacity-75 mt-1">Pembayaran yang sudah diterima</p>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Sudah Cair - Tahun Ini</p>
                <i class="fas fa-calendar-check text-xl opacity-75"></i>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($paymentStats['total_this_year'], 0, ',', '.') }}</h3>
            <p class="text-xs opacity-75 mt-1">Total pembayaran {{ date('Y') }}</p>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Rata-rata/Bulan</p>
                <i class="fas fa-chart-line text-xl opacity-75"></i>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($paymentStats['average_per_month'], 0, ',', '.') }}</h3>
            <p class="text-xs opacity-75 mt-1">Berdasarkan pembayaran cair</p>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Menunggu Finance ({{ $paymentStats['approved_unpaid_count'] }})</p>
                <i class="fas fa-clock text-xl opacity-75"></i>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($paymentStats['approved_unpaid_amount'], 0, ',', '.') }}</h3>
            <p class="text-xs opacity-75 mt-1">Disetujui, belum dibayar</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-sm p-5 text-white hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm opacity-90">Pending Admin ({{ $paymentStats['pending_count'] }})</p>
                <i class="fas fa-hourglass-half text-xl opacity-75"></i>
            </div>
            <h3 class="text-2xl font-bold">Rp {{ number_format($paymentStats['pending_amount'], 0, ',', '.') }}</h3>
            <p class="text-xs opacity-75 mt-1">Menunggu approval</p>
        </div>
    </div>

    <!-- Earnings Chart -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-chart-line mr-2 text-green-600"></i>Grafik Pendapatan</h5>
                <p class="text-sm text-gray-600">Riwayat pembayaran yang <strong>sudah cair</strong> (status: PAID)</p>
            </div>
            <div class="flex gap-2">
                <select id="chartPeriod" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="3">3 Bulan Terakhir</option>
                    <option value="6">6 Bulan Terakhir</option>
                    <option value="12" selected>12 Bulan Terakhir</option>
                </select>
            </div>
        </div>
        <div class="p-6">
            <div class="chart-container">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Ongoing Classes (Kelas Berjalan) -->
    @if($ongoingClasses->count() > 0)
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-chalkboard-teacher mr-2 text-purple-600"></i>Kelas Berjalan</h5>
                <p class="text-sm text-gray-600">Kelas yang sedang aktif berlangsung</p>
            </div>
            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-sm font-semibold rounded-full">
                {{ $ongoingClasses->count() }} Kelas
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nama Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Instansi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Jumlah Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Pertemuan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Trainer</th>
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
                    <tr class="hover:bg-purple-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $class->name }}</div>
                                @if($class->description)
                                <div class="text-sm text-gray-600 mt-1">{{ Str::limit($class->description, 50) }}</div>
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
                                <span class="text-sm text-gray-600">siswa</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-check text-gray-500"></i>
                                <span class="font-semibold text-gray-900">{{ $class->meet }}</span>
                                <span class="text-sm text-gray-600">x</span>
                            </div>
                            <div class="text-xs text-gray-600 mt-1">{{ $class->duration }} menit/sesi</div>
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
                            <div class="text-sm text-gray-900">
                                @if(is_array($class->trainer))
                                    @foreach($class->trainer as $trainer)
                                        <div class="mb-1">{{ $trainer }}</div>
                                    @endforeach
                                @else
                                    {{ $class->trainer }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="w-32">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-semibold text-gray-700">{{ $progressPercent }}%</span>
                                    <span class="text-xs text-gray-600">{{ $daysElapsed }}/{{ $totalDays }} hari</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Assigned Projects -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-project-diagram mr-2"></i>Recent Projects</h5>
            </div>
            <div class="p-6">
                @if($projects->count() > 0)
                    <div class="space-y-4">
                        @foreach($projects as $project)
                            <a href="{{ route('employee.projects.show', $project) }}" class="block p-4 rounded-lg border border-gray-200 hover:border-blue-400 hover:shadow-md transition">
                                <div class="flex justify-between items-center mb-2">
                                    <strong class="text-gray-900">{{ $project->project_name }}</strong>
                                    @if($project->status == 'in_progress')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">In Progress</span>
                                    @elseif($project->status == 'completed')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Completed</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">{{ ucfirst($project->status) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <small class="text-gray-600">{{ $project->project_code }}</small>
                                    <br>
                                    <small class="text-gray-600">Client: {{ $project->client->user->name ?? $project->client->company_name ?? 'N/A' }}</small>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Belum ada project yang di-assign</p>
                @endif
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-tasks mr-2"></i>Pending Tasks</h5>
            </div>
            <div class="p-6">
                @if($tasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($tasks as $task)
                            <div class="p-4 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <strong class="text-gray-900 block">{{ $task->title }}</strong>
                                        <small class="text-gray-600">{{ $task->project->project_name }}</small>
                                    </div>
                                    @if($task->priority == 'urgent')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Urgent</span>
                                    @elseif($task->priority == 'high')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">High</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">{{ ucfirst($task->priority) }}</span>
                                    @endif
                                </div>
                                @if($task->due_date)
                                    <small class="text-gray-600">
                                        <i class="far fa-calendar mr-1"></i>Due: {{ $task->due_date->format('d M Y') }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">Tidak ada task pending</p>
                @endif
            </div>
        </div>
    </div>

    <!-- All Projects History -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h5 class="text-lg font-bold text-gray-900"><i class="fas fa-history mr-2"></i>Riwayat Semua Project</h5>
                <p class="text-sm text-gray-600">Semua project yang pernah Anda kerjakan</p>
            </div>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                {{ $allProjects->count() }} Projects
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($allProjects as $project)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $project->project_name }}</div>
                                <div class="text-sm text-gray-600">{{ $project->project_code }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $project->client->user->name ?? $project->client->company_name ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded">
                                {{ $project->employee_role }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($project->status == 'completed')
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Completed
                                </span>
                            @elseif($project->status == 'in_progress')
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-spinner mr-1"></i>In Progress
                                </span>
                            @elseif($project->status == 'on_hold')
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">
                                    <i class="fas fa-pause-circle mr-1"></i>On Hold
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">
                                    {{ ucfirst($project->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                <i class="fas fa-clock text-gray-500 text-sm"></i>
                                <span class="font-semibold text-gray-900">{{ number_format($project->total_hours, 1) }}</span>
                                <span class="text-sm text-gray-600">jam</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600">
                                {{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}
                                @if($project->end_date)
                                    <br>s/d {{ $project->end_date->format('d M Y') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('employee.projects.show', $project) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                               title="Lihat Detail">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 opacity-50"></i>
                            <p>Belum ada project yang dikerjakan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Data for earnings chart
    const monthlyData = @json($monthlyPayments);
    
    let earningsChart;
    
    function createChart(months = 12) {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        
        // Filter data based on selected period
        const filteredData = monthlyData.slice(-months);
        
        // Destroy existing chart if it exists
        if (earningsChart) {
            earningsChart.destroy();
        }
        
        earningsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: filteredData.map(item => item.month),
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: filteredData.map(item => item.total),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(34, 197, 94)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverBackgroundColor: 'rgb(22, 163, 74)',
                    pointHoverBorderColor: '#fff',
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
                                size: 13,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value);
                            },
                            font: { size: 11 }
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
                            font: { size: 11 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    // Initialize chart
    createChart(12);
    
    // Period selector
    document.getElementById('chartPeriod').addEventListener('change', function() {
        const months = parseInt(this.value);
        createChart(months);
    });
</script>
@endpush
@endsection