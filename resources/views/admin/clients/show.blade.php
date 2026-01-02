@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $client->name ?: ($client->user->name ?? 'Client Detail') }}</h1>
            <p class="text-gray-600">Informasi lengkap client</p>
        </div>
        <a href="{{ route('admin.clients.edit', $client) }}" 
           class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            <i class="fas fa-edit mr-2"></i> Edit Data
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Client Info -->
        <div class="space-y-6">
            <!-- Client Profile -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mx-auto mb-4">
                        {{ strtoupper(substr($client->name ?: ($client->user->name ?? 'C'), 0, 2)) }}
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $client->name ?: ($client->user->name ?? 'N/A') }}</h3>
                    @if($client->company_name)
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-building mr-1"></i>{{ $client->company_name }}
                    </p>
                    @endif
                </div>

                <div class="space-y-3 border-t border-gray-200 pt-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-[#7b2cbf] mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Email</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $client->email ?: ($client->user->email ?? '-') }}</p>
                        </div>
                    </div>

                    @if($client->phone || $client->contact_phone)
                    <div class="flex items-start gap-3">
                        <i class="fab fa-whatsapp text-green-500 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">WhatsApp</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $client->phone ?: $client->contact_phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($client->address)
                    <div class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-red-500 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Alamat</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $client->address }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-start gap-3">
                        <i class="fas fa-calendar text-blue-500 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-600">Bergabung</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $client->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                @php
                    $clientPhone = $client->phone ?: $client->contact_phone;
                @endphp
                @if($clientPhone)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $clientPhone);
                        // Convert 08xxx to 628xxx for international format
                        if (substr($cleanPhone, 0, 1) === '0') {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}" 
                       target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-lg hover:from-green-700 hover:to-green-600 transition shadow-sm">
                        <i class="fab fa-whatsapp"></i>
                        Chat WhatsApp
                    </a>
                </div>
                @endif
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar text-[#7b2cbf] mr-2"></i>Statistik
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Orders</span>
                        <span class="text-lg font-bold text-blue-700">{{ $stats['total_orders'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Projects</span>
                        <span class="text-lg font-bold text-purple-700">{{ $stats['total_projects'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Projects</span>
                        <span class="text-lg font-bold text-green-700">{{ $stats['active_projects'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-lg font-bold text-gray-700">{{ $stats['completed_projects'] }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-800">Total Revenue</span>
                            <span class="text-xl font-bold text-[#7b2cbf]">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Orders & Projects -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Orders History -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-shopping-cart text-[#7b2cbf] mr-2"></i>Riwayat Orders
                </h3>
                
                @if($client->orders->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada order</p>
                @else
                <div class="space-y-3">
                    @foreach($client->orders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-[#7b2cbf] transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                                @if($order->payment_type === 'installment')
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-sync-alt mr-1"></i> Cicilan {{ $order->paid_installments }}/{{ $order->installment_count }}
                                </span>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status == 'pending_review') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        
                        <div class="space-y-1 mb-3">
                            @foreach($order->items as $item)
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-check text-green-500 mr-1"></i>
                                {{ $item->service->name }}
                            </p>
                            @endforeach
                        </div>

                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-gray-900">Total:</span>
                                <span class="text-lg font-bold text-[#7b2cbf]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                            @if($order->payment_type === 'installment' && $order->remaining_amount > 0)
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-600">Sisa Belum Lunas:</span>
                                <span class="font-semibold text-red-600">Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}</span>
                            </div>
                            @elseif($order->payment_type === 'installment' && $order->remaining_amount <= 0)
                            <div class="text-xs text-green-600 text-right">
                                <i class="fas fa-check-circle mr-1"></i> Sudah Lunas
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Projects -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-briefcase text-[#7b2cbf] mr-2"></i>Projects
                </h3>
                
                @if($client->projects->isEmpty())
                <p class="text-center text-gray-500 py-8">Belum ada project</p>
                @else
                <div class="space-y-3">
                    @foreach($client->projects as $project)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-[#7b2cbf] transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <a href="{{ route('admin.projects.show', $project) }}" class="font-bold text-gray-900 hover:text-[#7b2cbf]">
                                    {{ $project->project_name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $project->project_code }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($project->status == 'completed') bg-green-100 text-green-800
                                @elseif($project->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($project->status == 'on_hold') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>

                        {{-- Disabled - project_services table removed --}}
                        {{-- @if($project->services->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($project->services as $service)
                            <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                {{ $service->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif --}}

                        <div class="flex justify-between items-center text-xs text-gray-600 pt-2 border-t border-gray-200">
                            <span>Budget: Rp {{ number_format($project->budget, 0, ',', '.') }}</span>
                            <span>{{ $project->start_date ? $project->start_date->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
