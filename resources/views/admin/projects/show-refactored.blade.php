@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header with Status -->
    <x-project-header :project="$project" :deadline="$deadline" :user="auth()->user()" />

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
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
                <button type="button" 
                        onclick="openPaymentRequestModal()" 
                        class="bg-white text-green-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold shadow-md flex items-center gap-2"
                        aria-label="Ajukan payment request untuk project ini">
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

    <!-- Quick Actions for Admin -->
    @if(auth()->user()->isAdmin() && $project->presenter->isActive())
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h3 class="text-lg font-bold mb-1">Quick Actions</h3>
                <p class="text-sm opacity-90">Perform common actions quickly</p>
            </div>
            <div class="flex gap-3 flex-wrap">
                @if($project->status !== 'completed')
                <button onclick="markAsCompleted()" 
                        class="bg-white text-indigo-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition font-semibold shadow-md flex items-center gap-2"
                        aria-label="Tandai project sebagai selesai">
                    <i class="fas fa-check-circle"></i>
                    Mark as Completed
                </button>
                @endif
                
                @if($project->presenter->hasRemainingPayment())
                <button onclick="requestPayment()" 
                        class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition font-semibold shadow-md flex items-center gap-2"
                        aria-label="Kirim reminder pembayaran ke client via WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    Request Payment
                </button>
                @endif
                
                <button onclick="window.print()" 
                        class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition font-semibold shadow-md flex items-center gap-2"
                        aria-label="Print project report">
                    <i class="fas fa-print"></i>
                    Print Report
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Project Statistics -->
    <x-project-stats :project="$project" :stats="$stats" :deadline="$deadline" />

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
                            <div class="w-8 h-8 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-xs font-bold" aria-hidden="true">
                                {{ $project->presenter->getUserInitials($picMember->user->name) }}
                            </div>
                            <span class="text-gray-900 font-semibold">{{ $picMember->user->name }}</span>
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
                            <p class="text-sm font-semibold {{ $project->presenter->getBudgetColorClass($stats['budget_used_percentage']) }}">
                                {{ number_format($stats['budget_used_percentage'], 1) }}%
                            </p>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            @php
                                $percentage = min($stats['budget_used_percentage'], 100);
                            @endphp
                            <div class="{{ $project->presenter->getBudgetProgressBarColor($percentage) }} h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $percentage }}%"
                                 role="progressbar" 
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100"></div>
                        </div>
                        @if($stats['budget_used_percentage'] > 100)
                        <p class="text-xs text-red-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Over budget {{ $project->presenter->formatCurrency($project->actual_cost - $project->budget) }}
                        </p>
                        @endif
                    </div>

                    <!-- Status Notes -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-sm font-semibold text-gray-700">Catatan Status:</p>
                            @if(auth()->user()->isAdmin())
                            <button onclick="openNotesModal()" 
                                    class="text-xs text-blue-600 hover:text-blue-800"
                                    aria-label="Edit catatan status">
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
                    <div class="w-16 h-16 bg-[#7b2cbf] rounded-full flex items-center justify-center text-white text-xl font-bold" aria-hidden="true">
                        {{ $project->presenter->getClientInitials() }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">{{ $project->presenter->getClientName() }}</p>
                        <p class="text-sm text-gray-600">{{ $project->presenter->getClientEmail() }}</p>
                        <p class="text-sm text-gray-600">{{ $project->presenter->getClientPhone() }}</p>
                        @if($project->client->company_name)
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fas fa-building mr-1"></i> {{ $project->client->company_name }}
                        </p>
                        @endif
                    </div>
                    @if($project->presenter->getClientPhone())
                    <div class="flex flex-col gap-2">
                        <a href="{{ $project->presenter->getWhatsAppLink() }}" 
                           target="_blank" 
                           class="bg-gradient-to-r from-green-600 to-green-500 text-white px-3 py-2 rounded-lg hover:from-green-700 hover:to-green-600 transition shadow-sm text-xs text-center"
                           aria-label="Chat WhatsApp dengan client">
                            <i class="fab fa-whatsapp mr-1"></i> WhatsApp
                        </a>
                        <a href="tel:{{ $project->presenter->getClientPhone() }}" 
                           class="bg-gradient-to-r from-blue-600 to-blue-500 text-white px-3 py-2 rounded-lg hover:from-blue-700 hover:to-blue-600 transition shadow-sm text-xs text-center"
                           aria-label="Telepon client">
                            <i class="fas fa-phone mr-1"></i> Call
                        </a>
                        @if($project->presenter->getClientEmail())
                        <a href="mailto:{{ $project->presenter->getClientEmail() }}" 
                           class="bg-gradient-to-r from-gray-600 to-gray-500 text-white px-3 py-2 rounded-lg hover:from-gray-700 hover:to-gray-600 transition shadow-sm text-xs text-center"
                           aria-label="Email ke client">
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
                            <p class="font-semibold text-gray-900">{{ $project->presenter->formatCurrency($item->subtotal) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-[#7b2cbf]">{{ $project->presenter->formatCurrency($project->order->total_amount) }}</span>
                    </div>
                    
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
                            <span class="text-lg font-bold text-red-600">{{ $project->presenter->formatCurrency($project->order->remaining_amount) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Project Expenses Component -->
            <x-project-expenses :project="$project" :stats="$stats" />

            <!-- Activity Timeline -->
            @if(isset($activities) && $activities->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-history mr-2 text-indigo-600"></i> Timeline Aktivitas
                </h2>
                
                <div class="space-y-3">
                    @foreach($activities as $activity)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600" aria-hidden="true">
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
                            <p class="text-xs text-gray-500">
                                <time datetime="{{ $activity->created_at->toIso8601String() }}">
                                    {{ $activity->created_at->diffForHumans() }}
                                </time>
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Team & Chat -->
        <div class="space-y-6">
            <!-- Team Members Component -->
            <x-project-team :project="$project" />

            <!-- Recent Tasks -->
            @if($project->tasks->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Tasks</h2>
                
                <div class="space-y-3">
                    @foreach($project->tasks as $task)
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

            <!-- Project Chat Component -->
            <x-project-chat :project="$project" :chats="$chats" />
        </div>
    </div>
</div>

<!-- Modals -->
@if(auth()->user()->isAdmin())
@include('admin.projects.partials.modals')
@else
@include('employee.projects.partials.payment-request-modal')
@endif

@push('scripts')
<script>
// Quick Actions Functions
function markAsCompleted() {
    if (confirm('Are you sure you want to mark this project as completed? This will update the status and set the completion date.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.projects.markCompleted", $project) }}';
        form.innerHTML = `@csrf @method('PATCH')`;
        document.body.appendChild(form);
        form.submit();
    }
}

function requestPayment() {
    const message = {!! json_encode($project->presenter->getPaymentRequestMessage()) !!};
    const phone = '{{ $project->presenter->getCleanPhone() }}';
    const clientName = '{{ $project->presenter->getClientName() }}';
    
    if (!phone) {
        alert('Client phone number not found!');
        return;
    }
    
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank');
    
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

// Auto-hide messages after 5 seconds
setTimeout(() => {
    const alerts = document.querySelectorAll('[role="alert"]');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>
@endpush
@endsection
