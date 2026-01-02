@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-indigo-600"></i>
                        Laporan Project
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">Export data project dalam format CSV</p>
                </div>
            </div>
        </div>

        <!-- Export Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-file-excel text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Export Data ke Excel</h2>
                    <p class="text-sm text-gray-600">Pilih filter untuk mengexport data project</p>
                </div>
            </div>

            <form action="{{ route('admin.laporan.export') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Year Filter -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>Filter Tahun
                        </label>
                        <select name="year" id="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Semua Tahun</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-check mr-2 text-indigo-600"></i>Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" id="start_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-calendar-times mr-2 text-indigo-600"></i>Tanggal Akhir
                        </label>
                        <input type="date" name="end_date" id="end_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Export Button -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <button type="button" onclick="resetForm()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 flex items-center">
                        <i class="fas fa-redo mr-2"></i>Reset
                    </button>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        <i class="fas fa-file-excel mr-2"></i>Export ke Excel
                    </button>
                </div>
            </form>
        </div>


        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
            @php
                $totalProjects = \App\Models\Project::count();
                $completedProjects = \App\Models\Project::where('status', 'completed')->count();
                $activeProjects = \App\Models\Project::where('status', 'active')->count();
                $totalRevenue = \App\Models\Order::where('payment_status', 'paid')->sum('paid_amount');
            @endphp

            <!-- Total Projects -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Project</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProjects }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completed Projects -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Project Selesai</p>
                        <p class="text-2xl font-bold text-green-600">{{ $completedProjects }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Projects -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Project Aktif</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $activeProjects }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-spinner text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Pendapatan</p>
                        <p class="text-xl font-bold text-purple-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('year').value = '';
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
}
</script>
@endsection
