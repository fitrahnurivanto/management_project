@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route(auth()->user()->isAdmin() ? 'admin.projects.index' : 'employee.projects.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_name }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($project->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($project->status === 'in_progress') bg-blue-100 text-blue-800
                    @elseif($project->status === 'completed') bg-green-100 text-green-800
                    @elseif($project->status === 'on_hold') bg-orange-100 text-orange-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
                
                @if($project->status !== 'completed' && $project->status !== 'cancelled' && $deadline)
                    @if($deadline['is_overdue'])
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 animate-pulse">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Terlambat {{ abs($deadline['days_left']) }} hari
                        </span>
                    @elseif($deadline['is_urgent'])
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 animate-pulse">
                            <i class="fas fa-clock mr-1"></i> URGENT - {{ $deadline['days_left'] }} hari lagi!
                        </span>
                    @endif
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>{{ $project->project_code }}</span>
                @if($project->pks_number)
                <span class="flex items-center"><i class="fas fa-file-contract mr-1"></i> {{ $project->pks_number }}</span>
                @endif
                @if($project->order)
                <span class="flex items-center"><i class="fas fa-receipt mr-1"></i> {{ $project->order->order_number }}</span>
                @endif
            </div>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex gap-3">
            <form action="{{ route('admin.projects.updateStatus', $project) }}" method="POST">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                    <option value="pending" {{ $project->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="on_hold" {{ $project->status === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $project->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </form>
        </div>
        @endif
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

    <!-- Quick Actions for Employee -->
    @if(!auth()->user()->isAdmin())
    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h3 class="text-lg font-bold mb-1">My Actions</h3>
                <p class="text-sm opacity-90">Ajukan pembayaran untuk project ini</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                <button type="button" onclick="openPaymentRequestModal()" 
                        class="bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold shadow-md flex items-center gap-2">
                    <i class="fas fa-money-bill-wave"></i>
                    Ajukan Payment Request
                </button>
                <a href="{{ route('employee.payment-requests.index') }}" 
                   class="bg-white text-teal-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold shadow-md flex items-center gap-2">
                    <i class="fas fa-history"></i>
                    Riwayat Request
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    @if(auth()->user()->isAdmin() && $project->status !== 'cancelled')
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h3 class="text-lg font-bold mb-1">Quick Actions</h3>
                <p class="text-sm opacity-90">Perform common actions quickly</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                @if($project->status !== 'completed')
                <button onclick="markAsCompleted()" 
                        class="bg-white text-indigo-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold shadow-md flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Mark as Completed
                </button>
                @endif
                
                @if($project->order && $project->order->remaining_amount > 0)
                <button onclick="requestPayment()" 
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition font-semibold shadow-md flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i>
                    Request Payment
                </button>
                @endif
                
                <button onclick="window.print()" 
                        class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition font-semibold shadow-md flex items-center gap-2">
                    <i class="fas fa-print"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Project Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Budget</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($project->budget, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Actual Cost</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($project->actual_cost, 0) }}</p>
                    @php
                        $budgetUsed = $stats['budget_used_percentage'];
                        $colorClass = $budgetUsed < 50 ? 'text-green-600' : ($budgetUsed < 80 ? 'text-yellow-600' : ($budgetUsed < 100 ? 'text-orange-600' : 'text-red-600'));
                    @endphp
                    <p class="text-xs {{ $colorClass }} font-semibold">{{ number_format($budgetUsed, 1) }}% used</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-money-bill text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Deadline</p>
                    @if($deadline)
                        @if($project->status === 'completed')
                            <p class="text-lg font-bold text-green-600">Selesai</p>
                            <p class="text-xs text-gray-600">{{ $deadline['formatted_date'] }}</p>
                        @elseif($deadline['is_overdue'])
                            <p class="text-lg font-bold text-red-600">Terlambat</p>
                            <p class="text-xs text-red-600">{{ abs($deadline['days_left']) }} hari lewat</p>
                        @elseif($deadline['is_today'])
                            <p class="text-lg font-bold text-orange-600">Hari Ini!</p>
                            <p class="text-xs text-gray-600">{{ $deadline['formatted_date'] }}</p>
                        @else
                            <p class="text-lg font-bold {{ $deadline['is_urgent'] ? 'text-orange-600' : 'text-blue-600' }}">{{ $deadline['days_left'] }} hari</p>
                            <p class="text-xs text-gray-600">{{ $deadline['formatted_date'] }}</p>
                        @endif
                    @else
                        <p class="text-lg font-bold text-gray-400">-</p>
                        <p class="text-xs text-gray-500">Belum ditentukan</p>
                    @endif
                </div>
                <div class="w-12 h-12 {{ !$deadline ? 'bg-gray-100' : ($deadline['is_overdue'] ? 'bg-red-100' : ($deadline['is_urgent'] ? 'bg-orange-100' : 'bg-purple-100')) }} rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt {{ !$deadline ? 'text-gray-400' : ($deadline['is_overdue'] ? 'text-red-600' : ($deadline['is_urgent'] ? 'text-orange-600' : 'text-purple-600')) }} text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_tasks'] }}</p>
                    <p class="text-xs text-green-600">{{ $stats['completed_tasks'] }} completed</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit Calculation Card -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl shadow-sm p-6 mb-6 border border-green-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Budget Project</p>
                <p class="text-xl font-bold text-gray-900">Rp {{ number_format($project->budget, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
                <p class="text-xl font-bold text-orange-600">Rp {{ number_format($stats['total_expenses'], 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Sisa Budget</p>
                @php
                    $remaining = $project->budget - $stats['total_expenses'];
                @endphp
                <p class="text-xl font-bold {{ $remaining >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                    Rp {{ number_format($remaining, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Profit/Omset</p>
                @php
                    $profit = $project->budget - $stats['total_expenses'];
                @endphp
                <p class="text-2xl font-bold {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($profit, 0, ',', '.') }}
                </p>
                <p class="text-xs {{ $profit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas {{ $profit >= 0 ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                    {{ $profit >= 0 ? 'Profit' : 'Rugi' }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Project Details & Client Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Details -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Detail Project</h2>
                    @if($project->duration)
                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-semibold">
                        <i class="fas fa-hourglass-half mr-1"></i> {{ $project->duration }}
                    </span>
                    @endif
                </div>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Deskripsi</p>
                        <p class="text-gray-900">{{ $project->description ?? '-' }}</p>
                    </div>

                    @if($picMember)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">PIC (Penanggung Jawab)</p>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($picMember->user->name ?? 'NA', 0, 2)) }}
                            </div>
                            <span class="text-gray-900 font-semibold">{{ $picMember->user->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @endif

                    @if($project->client->referral_source)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Sumber Referensi</p>
                        <p class="text-gray-900"><i class="fas fa-link mr-1 text-indigo-600"></i> {{ $project->client->referral_source }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Start Date</p>
                            <p class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 mb-1">End Date</p>
                            <p class="text-gray-900 font-semibold">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d F Y') : '-' }}</p>
                        </div>
                    </div>

                    @if($project->completed_at)
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completed At</p>
                        <p class="text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($project->completed_at)->format('d F Y H:i') }}</p>
                    </div>
                    @endif

                    <!-- Budget Progress Bar -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-sm text-gray-600">Budget Usage</p>
                            <p class="text-sm font-semibold {{ $stats['budget_used_percentage'] > 100 ? 'text-red-600' : ($stats['budget_used_percentage'] > 80 ? 'text-orange-600' : 'text-green-600') }}">
                                {{ number_format($stats['budget_used_percentage'], 1) }}%
                            </p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = min($stats['budget_used_percentage'], 100);
                                $bgColor = $percentage < 50 ? 'bg-green-500' : ($percentage < 80 ? 'bg-yellow-500' : ($percentage < 100 ? 'bg-orange-500' : 'bg-red-500'));
                            @endphp
                            <div class="{{ $bgColor }} h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                        @if($stats['budget_used_percentage'] > 100)
                        <p class="text-xs text-red-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Over budget Rp {{ number_format($project->actual_cost - $project->budget, 0) }}
                        </p>
                        @endif
                    </div>

                    <!-- Status Notes -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-semibold text-gray-700">Catatan Status:</p>
                            @if(auth()->user()->isAdmin())
                            <button onclick="openNotesModal()" class="text-xs text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                            @endif
                        </div>
                        <p class="text-sm text-gray-900">{{ $project->status_notes ?? 'Tidak ada catatan' }}</p>
                    </div>
                </div>
            </div>

            <!-- Client Information -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Client</h2>
                
                @if($project->client)
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-xl font-bold">
                        @php
                            $clientName = $project->client->name ?? $project->client->user->name ?? $project->client->company_name ?? 'N/A';
                        @endphp
                        {{ strtoupper(substr($clientName, 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $project->client->name ?? $project->client->user->name ?? $project->client->company_name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">{{ $project->client->email ?? $project->client->user->email ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $project->client->phone ?? '-' }}</p>
                        @if($project->client->company_name)
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-building mr-1"></i> {{ $project->client->company_name }}
                        </p>
                        @endif
                    </div>
                    @if($project->client->phone)
                    <div class="flex flex-col gap-2">
                        @php
                            $cleanPhone = preg_replace('/[^0-9]/', '', $project->client->phone);
                            if (substr($cleanPhone, 0, 1) === '0') {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            $clientEmail = $project->client->email ?? $project->client->user->email ?? '';
                        @endphp
                        <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-2 rounded-lg hover:from-green-700 hover:to-green-600 transition shadow-sm text-xs text-center">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <a href="tel:{{ $project->client->phone }}" class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-3 py-2 rounded-lg hover:from-blue-700 hover:to-blue-600 transition shadow-sm text-xs text-center">
                            <i class="fas fa-phone mr-1"></i> Call
                        </a>
                        @if($clientEmail)
                        <a href="mailto:{{ $clientEmail }}" class="bg-gradient-to-r from-gray-600 to-gray-500 text-white px-3 py-2 rounded-lg hover:from-gray-700 hover:to-gray-600 transition shadow-sm text-xs text-center">
                            <i class="fas fa-envelope mr-1"></i> Email
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
                @else
                <p class="text-sm text-gray-500">Data client tidak tersedia</p>
                @endif
            </div>

            <!-- Order Items -->
            @if($project->order && $project->order->items->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Layanan yang Dipesan</h2>
                
                <div class="space-y-3">
                    @foreach($project->order->items as $item)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $item->service->name ?? 'N/A' }}</p>
                            @if($item->servicePackage)
                            <p class="text-sm text-gray-600">Paket: {{ $item->servicePackage->name }}</p>
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Qty: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-[#7b2cbf]">Rp {{ number_format($project->order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    <!-- Payment Type & Status -->
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">Tipe Pembayaran:</span>
                        @if($project->order->payment_type === 'installment')
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-sync-alt mr-1"></i> Cicilan {{ $project->order->paid_installments }}/{{ $project->order->installment_count }}
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-money-bill-wave mr-1"></i> Lunas
                        </span>
                        @endif
                    </div>
                    
                    @if($project->order->payment_type === 'installment')
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Status Pembayaran:</span>
                            @if($project->order->remaining_amount > 0)
                            <span class="text-sm font-semibold text-orange-600">Belum Lunas</span>
                            @else
                            <span class="text-sm font-semibold text-green-600">
                                <i class="fas fa-check-circle mr-1"></i> Lunas
                            </span>
                            @endif
                        </div>
                        @if($project->order->remaining_amount > 0)
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-600">Sisa Belum Lunas:</span>
                            <span class="text-lg font-bold text-red-600">Rp {{ number_format($project->order->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Layanan yang Dipesan</h2>
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Tidak ada layanan yang dipesan</p>
                </div>
            </div>
            @endif

            <!-- Project Expenses -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-receipt mr-2 text-indigo-600"></i> Project Expenses
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Track all expenses for this project</p>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <button onclick="openAddExpenseModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Expense
                    </button>
                    @endif
                </div>

                @if($project->expenses->isEmpty())
                <div class="text-center py-12">
                    <i class="fas fa-wallet text-5xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">Belum ada expense tercatat</p>
                    @if(auth()->user()->isAdmin())
                    <button onclick="openAddExpenseModal()" class="mt-4 text-indigo-600 hover:text-indigo-800 font-semibold">
                        + Tambah Expense Pertama
                    </button>
                    @endif
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Receipt</th>
                                @if(auth()->user()->isAdmin())
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($project->expenses()->orderBy('expense_date', 'desc')->get() as $expense)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if(str_contains(strtolower($expense->expense_type), 'honor')) bg-purple-100 text-purple-800
                                        @elseif(str_contains(strtolower($expense->expense_type), 'tool')) bg-blue-100 text-blue-800
                                        @elseif(str_contains(strtolower($expense->expense_type), 'ads')) bg-red-100 text-red-800
                                        @elseif(str_contains(strtolower($expense->expense_type), 'freelancer')) bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $expense->expense_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $expense->description ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-right text-gray-900">
                                    Rp {{ number_format($expense->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($expense->receipt_file)
                                    <a href="{{ Storage::url($expense->receipt_file) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                @if(auth()->user()->isAdmin())
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openEditExpenseModal({{ $expense->id }}, '{{ $expense->expense_type }}', '{{ $expense->description }}', {{ $expense->amount }}, '{{ $expense->expense_date }}')" 
                                                class="text-blue-600 hover:text-blue-800">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.projects.expenses.delete', [$project, $expense]) }}" method="POST" 
                                              onsubmit="return confirm('Yakin ingin menghapus expense ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-900">Total Expenses:</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-600 text-lg">
                                    Rp {{ number_format($stats['total_expenses'], 0, ',', '.') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

            <!-- Activity Timeline -->
            @if(isset($activities) && $activities->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-history mr-2 text-indigo-600"></i> Timeline Aktivitas
                </h2>
                
                <div class="space-y-3">
                    @foreach($activities as $activity)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                            @if(str_contains($activity->action, 'create'))
                                <i class="fas fa-plus text-xs"></i>
                            @elseif(str_contains($activity->action, 'update'))
                                <i class="fas fa-edit text-xs"></i>
                            @elseif(str_contains($activity->action, 'delete'))
                                <i class="fas fa-trash text-xs"></i>
                            @else
                                <i class="fas fa-check text-xs"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                            <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Team Management -->
        <div class="space-y-6">
            <!-- Team Members -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Tim Project</h2>
                    @if(auth()->user()->isAdmin())
                    <button onclick="openAddMemberModal()" class="bg-[#7b2cbf] text-white px-3 py-1 rounded-lg hover:bg-[#6a25a8] transition text-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </button>
                    @endif
                </div>

                @if($project->teams->isEmpty() || $project->teams->sum(fn($t) => $t->members->count()) === 0)
                <div class="text-center py-8">
                    <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500">Belum ada tim yang ditugaskan</p>
                </div>
                @else
                <div class="space-y-3">
                    @foreach($project->teams as $team)
                        @foreach($team->members as $member)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">{{ $member->user->name }}</p>
                                    <p class="text-xs text-gray-600">{{ ucfirst($member->role) }}</p>
                                </div>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <form action="{{ route('admin.projects.removeTeamMember', [$project, $member]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus {{ $member->user->name ?? 'member ini' }} dari tim? Tindakan ini tidak dapat dibatalkan.')" class="remove-member-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm" data-loading="false">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                        @endforeach
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Project Services -->
            {{-- Disabled - project_services table removed --}}
            {{-- @if($project->services->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Services</h2>
                
                <div class="space-y-3">
                    @foreach($project->services as $service)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="font-semibold text-gray-900 text-sm">{{ $service->name }}</p>
                        <p class="text-xs text-gray-600 mt-1">Budget: Rp {{ number_format($service->pivot->allocated_budget, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif --}}

            <!-- Recent Tasks -->
            @if($project->tasks->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Tasks</h2>
                
                <div class="space-y-3">
                    @foreach($project->tasks->take(5) as $task)
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-semibold text-gray-900 text-sm">{{ $task->task_name }}</p>
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                        @if($task->assignee)
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-user mr-1"></i> {{ $task->assignee->name }}
                        </p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Project Chat -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col" style="height: 600px;">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-t-xl">
                    <h5 class="text-lg font-semibold text-white">
                        <i class="fas fa-comments mr-2"></i>Project Chat
                    </h5>
                    <p class="text-xs text-indigo-100 mt-1">Global - Client, Team & Admin</p>
                </div>
                
                <!-- Chat Messages -->
                <div class="flex-1 overflow-y-auto p-4" id="chatMessages">
                    @foreach($chats as $chat)
                        <div class="mb-4 {{ $chat->user_id == auth()->id() ? 'text-right' : '' }}">
                            <div class="inline-block max-w-[80%]">
                                <div class="flex items-center mb-1 {{ $chat->user_id == auth()->id() ? 'justify-end' : '' }}">
                                    <strong class="{{ $chat->user_id == auth()->id() ? 'text-indigo-600' : 'text-gray-900' }} text-sm">
                                        {{ $chat->user->name }}
                                    </strong>
                                    <small class="text-gray-500 ml-2 text-xs">{{ $chat->created_at->format('H:i') }}</small>
                                </div>
                                <div class="p-3 rounded-lg text-sm {{ $chat->user_id == auth()->id() ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none' }}">
                                    {{ $chat->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Chat Input -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                    <form id="chatForm" class="flex gap-2">
                        @csrf
                        <input type="text" 
                               id="messageInput" 
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" 
                               placeholder="Ketik pesan..." 
                               required>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-semibold transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Team Member Modal -->
@if(auth()->user()->isAdmin())
<!-- Edit Status Notes Modal -->
<div id="notesModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Catatan Status Project</h3>
                <button onclick="closeNotesModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.updateNotes', $project) }}" method="POST" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Status</label>
                <textarea name="status_notes" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]" placeholder="Masukkan catatan terkait status project saat ini...">{{ $project->status_notes }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Contoh: "Menunggu approval client", "Revisi ke-2", "Sedang finalisasi mockup"</p>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeNotesModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-[#7b2cbf] text-white px-4 py-2 rounded-lg hover:bg-[#6a25a8] transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="addMemberModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Tambah Anggota Tim</h3>
                <button onclick="closeAddMemberModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.assignTeamMember', $project) }}" method="POST" class="p-6">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Employee</label>
                <select name="user_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                    <option value="">-- Pilih Employee --</option>
                    @if(isset($availableEmployees))
                        @foreach($availableEmployees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->email }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role dalam Tim</label>
                <select name="role" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf]">
                    <option value="pic">PIC</option>
                    <option value="project_manager">Project Manager</option>
                    <option value="content_creator">Content Creator</option>
                    <option value="developer">Developer</option>
                    <option value="designer">Designer</option>
                    <option value="marketing">Marketing</option>
                    <option value="seo_specialist">SEO Specialist</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeAddMemberModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-[#7b2cbf] text-white px-4 py-2 rounded-lg hover:bg-[#6a25a8] transition">
                    Tambahkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="addExpenseModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Tambah Expense Baru</h3>
                <button onclick="closeAddExpenseModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('admin.projects.expenses.store', $project) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="expense_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Honor/Gaji">Honor/Gaji</option>
                        <option value="Tools & Software">Tools & Software</option>
                        <option value="Advertising/Iklan">Advertising/Iklan</option>
                        <option value="Freelancer">Freelancer</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Material">Material</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Gaji designer bulan Desember, FB Ads 3 hari, dll"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="text" name="amount_display" id="add_amount" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="500.000" oninput="formatCurrency(this)">
                    <input type="hidden" name="amount" id="add_amount_raw">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="expense_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Receipt (Optional)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Max 2MB - PDF, JPG, PNG</p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeAddExpenseModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Expense Modal -->
<div id="editExpenseModal" class="hidden fixed inset-0 bg-transparent flex items-center justify-center z-50" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Edit Expense</h3>
                <button onclick="closeEditExpenseModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <form id="editExpenseForm" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PATCH')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                    <select name="expense_type" id="edit_expense_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Honor/Gaji">Honor/Gaji</option>
                        <option value="Tools & Software">Tools & Software</option>
                        <option value="Advertising/Iklan">Advertising/Iklan</option>
                        <option value="Freelancer">Freelancer</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Material">Material</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" id="edit_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah (Rp)</label>
                    <input type="text" name="amount_display" id="edit_amount" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" oninput="formatCurrency(this)">
                    <input type="hidden" name="amount" id="edit_amount_raw">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="expense_date" id="edit_expense_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Receipt Baru (Optional)</label>
                    <input type="file" name="receipt_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeEditExpenseModal()" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function openAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function closeAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}

function openNotesModal() {
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
}

// Quick Actions Functions
function markAsCompleted() {
    if (confirm('Are you sure you want to mark this project as completed? This will update the status and set the completion date.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.projects.markCompleted", $project) }}';
        form.innerHTML = `
            @csrf
            @method('PATCH')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function requestPayment() {
    const clientName = '{{ $project->client->name ?? $project->client->user->name ?? $project->client->company_name ?? "Client" }}';
    const projectName = '{{ $project->project_name }}';
    const projectCode = '{{ $project->project_code }}';
    const totalAmount = 'Rp {{ number_format($project->order->total_amount ?? 0, 0, ",", ".") }}';
    const paidAmount = 'Rp {{ number_format(($project->order->total_amount ?? 0) - ($project->order->remaining_amount ?? 0), 0, ",", ".") }}';
    const remainingAmount = 'Rp {{ number_format($project->order->remaining_amount ?? 0, 0, ",", ".") }}';
    const phone = '{{ $project->client->phone ?? "" }}';
    
    if (!phone) {
        alert('Client phone number not found!');
        return;
    }
    
    let cleanPhone = phone.replace(/[^0-9]/g, '');
    if (cleanPhone.startsWith('0')) {
        cleanPhone = '62' + cleanPhone.substring(1);
    }
    
    const message = 
        `Halo Kak *${clientName}*\n\n` +
        `Kabar baik dari kami! Project Anda sudah hampir selesai\n\n` +
        `*DETAIL PROJECT*\n` +
        `Project: *${projectName}*\n` +
        `Kode: ${projectCode}\n\n` +
        `----------------------------\n` +
        `*INFORMASI PEMBAYARAN*\n` +
        `Total Project: ${totalAmount}\n` +
        `Sudah Dibayar: ${paidAmount}\n` +
        `*Sisa Belum Lunas: ${remainingAmount}*\n` +
        `----------------------------\n\n` +
        `Untuk kelancaran proses penyelesaian, mohon bantuannya untuk segera melunasi sisa pembayaran ya Kak\n\n` +
        `_Dengan pelunasan tepat waktu, kami bisa langsung finalisasi project Anda!_\n\n` +
        `Ada pertanyaan atau butuh bantuan? Jangan ragu untuk hubungi kami ya!\n\n` +
        `Terima kasih atas kepercayaan dan kerjasamanya!`;
    
    window.open(`https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`, '_blank');
    
    // Send notification
    window.dispatchEvent(new CustomEvent('notify', {
        detail: {
            title: 'Payment Request Sent',
            message: `WhatsApp message sent to ${clientName}`,
            type: 'success',
            icon: 'fab fa-whatsapp'
        }
    }));
}

// Close modal when clicking outside
document.getElementById('addMemberModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddMemberModal();
    }
});

document.getElementById('notesModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeNotesModal();
    }
});

document.getElementById('addExpenseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAddExpenseModal();
    }
});

document.getElementById('editExpenseModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditExpenseModal();
    }
});

// Expense Modal Functions
function openAddExpenseModal() {
    document.getElementById('addExpenseModal').classList.remove('hidden');
}

function closeAddExpenseModal() {
    document.getElementById('addExpenseModal').classList.add('hidden');
}

function openEditExpenseModal(expenseId, expenseType, description, amount, expenseDate) {
    const modal = document.getElementById('editExpenseModal');
    const form = document.getElementById('editExpenseForm');
    
    // Set form action
    form.action = `/admin/projects/{{ $project->id }}/expenses/${expenseId}`;
    
    // Populate form fields
    document.getElementById('edit_expense_type').value = expenseType;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_amount').value = formatNumber(amount);
    document.getElementById('edit_amount_raw').value = amount;
    document.getElementById('edit_expense_date').value = expenseDate;
    
    modal.classList.remove('hidden');
}

function closeEditExpenseModal() {
    document.getElementById('editExpenseModal').classList.add('hidden');
}

// Format currency input dengan thousand separator (titik)
function formatCurrency(input) {
    let value = input.value.replace(/\D/g, ''); // Hapus semua non-digit
    if (value) {
        input.value = formatNumber(value);
        // Set hidden input dengan value asli (tanpa format)
        const hiddenId = input.id.replace('add_amount', 'add_amount_raw').replace('edit_amount', 'edit_amount_raw');
        document.getElementById(hiddenId).value = value;
    } else {
        input.value = '';
    }
}

// Format number dengan thousand separator
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function deleteExpense(expenseId) {
    if (confirm('Yakin ingin menghapus expense ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/projects/{{ $project->id }}/expenses/${expenseId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-hide success/error messages after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);

// ===== CHAT FUNCTIONALITY =====
const chatMessages = document.getElementById('chatMessages');
const chatForm = document.getElementById('chatForm');
const messageInput = document.getElementById('messageInput');
const projectId = {{ $project->id }};

// Scroll to bottom
function scrollToBottom() {
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Initial scroll
if (chatMessages) {
    scrollToBottom();
}

// Send message
if (chatForm) {
    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch(`/admin/projects/${projectId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            if (response.ok) {
                const chat = await response.json();
                appendMessage(chat);
                messageInput.value = '';
                scrollToBottom();
            } else {
                console.error('Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    });
}

// Append new message
function appendMessage(chat) {
    const isMe = chat.user_id == {{ auth()->id() }};
    const messageHtml = `
        <div class="mb-4 ${isMe ? 'text-right' : ''}">
            <div class="inline-block max-w-[80%]">
                <div class="flex items-center mb-1 ${isMe ? 'justify-end' : ''}">
                    <strong class="${isMe ? 'text-indigo-600' : 'text-gray-900'} text-sm">
                        ${chat.user.name}
                    </strong>
                    <small class="text-gray-500 ml-2 text-xs">${new Date().toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                </div>
                <div class="p-3 rounded-lg text-sm ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                    ${chat.message}
                </div>
            </div>
        </div>
    `;
    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
}

// Refresh chat every 5 seconds
setInterval(async () => {
    try {
        const response = await fetch(`/admin/projects/${projectId}/chats`);
        if (response.ok) {
            const chats = await response.json();
            refreshChatMessages(chats);
        }
    } catch (error) {
        console.error('Error refreshing chat:', error);
    }
}, 5000);

function refreshChatMessages(chats) {
    const currentScroll = chatMessages.scrollTop;
    const isAtBottom = chatMessages.scrollHeight - chatMessages.scrollTop === chatMessages.clientHeight;
    
    chatMessages.innerHTML = '';
    chats.forEach(chat => {
        const isMe = chat.user_id == {{ auth()->id() }};
        const messageHtml = `
            <div class="mb-4 ${isMe ? 'text-right' : ''}">
                <div class="inline-block max-w-[80%]">
                    <div class="flex items-center mb-1 ${isMe ? 'justify-end' : ''}">
                        <strong class="${isMe ? 'text-indigo-600' : 'text-gray-900'} text-sm">
                            ${chat.user.name}
                        </strong>
                        <small class="text-gray-500 ml-2 text-xs">${new Date(chat.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</small>
                    </div>
                    <div class="p-3 rounded-lg text-sm ${isMe ? 'bg-indigo-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-900 rounded-bl-none'}">
                        ${chat.message}
                    </div>
                </div>
            </div>
        `;
        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    });
    
    if (isAtBottom) {
        scrollToBottom();
    }
}
// ===== END CHAT FUNCTIONALITY =====

// Payment Request Modal for Employee
function openPaymentRequestModal() {
    console.log('Opening payment request modal...');
    const modal = document.getElementById('paymentRequestModal');
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal opened successfully');
    } else {
        console.error('Modal element not found!');
    }
}

function closePaymentRequestModal() {
    document.getElementById('paymentRequestModal').classList.add('hidden');
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = formatNumber(value);
        document.getElementById('payment_amount_raw').value = value;
    } else {
        input.value = '';
        document.getElementById('payment_amount_raw').value = '';
    }
}
</script>

<!-- Payment Request Modal (Employee Only) -->
@if(!auth()->user()->isAdmin())
<div id="paymentRequestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Ajukan Payment Request</h3>
                <button onclick="closePaymentRequestModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        
        <form action="{{ route('employee.payment-requests.store') }}" method="POST">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <input type="hidden" name="requested_amount" id="payment_amount_raw">
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Project</label>
                    <input type="text" value="{{ $project->project_name }}" disabled 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nominal Payment <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="text" id="payment_amount" oninput="formatCurrencyInput(this)" required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="0">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nominal yang ingin diajukan</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Optional)</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Keterangan pekerjaan yang sudah diselesaikan..."></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button type="button" onclick="closePaymentRequestModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="fas fa-paper-plane mr-2"></i>Ajukan
                </button>
            </div>
        </form>
    </div>
</div>
@endif
</script>
@endsection
