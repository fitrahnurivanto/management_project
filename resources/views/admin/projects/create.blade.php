@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.projects.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Project Baru</h1>
            <p class="text-gray-600">Buat project dari order yang sudah dikonfirmasi</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Mohon perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
        <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak Ada Order yang Tersedia</h3>
        <p class="text-gray-500 mb-6">Tidak ada order dengan status "Paid" yang belum dibuatkan project.</p>
        <a href="{{ route('admin.orders.index') }}" class="inline-block bg-[#7b2cbf] text-white px-6 py-3 rounded-lg hover:bg-[#6a25a8] transition">
            <i class="fas fa-shopping-cart mr-2"></i> Lihat Orders
        </a>
    </div>
    @else
    <form action="{{ route('admin.projects.store') }}" method="POST" class="space-y-6" id="projectForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order Selection -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Select Order -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-shopping-cart text-[#7b2cbf] mr-2"></i> Pilih Order
                    </h2>
                    
                    <div class="space-y-3">
                        @foreach($orders as $order)
                        <label class="block cursor-pointer">
                            <input type="radio" name="order_id" value="{{ $order->id }}" 
                                   class="peer hidden" 
                                   onchange="selectOrder({{ $order->id }})"
                                   {{ old('order_id') == $order->id ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 rounded-xl p-4 hover:border-[#7b2cbf] peer-checked:border-[#7b2cbf] peer-checked:bg-purple-50 transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="font-bold text-gray-900">Order #{{ $order->order_number }}</p>
                                        <p class="text-sm text-gray-600">{{ $order->client->user->name ?? $order->client->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->client->email }} • {{ $order->client->phone }}</p>
                                    </div>
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                        PAID
                                    </span>
                                </div>
                                
                                <!-- Order Items -->
                                <div class="mt-3 pt-3 border-t border-gray-200">
                                    <p class="text-xs font-semibold text-gray-700 mb-2">Layanan:</p>
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-700">
                                                <i class="fas fa-check text-green-500 mr-1"></i>
                                                {{ $item->service->name }}
                                                @if($item->package)
                                                <span class="text-xs text-gray-500">({{ $item->package->name }})</span>
                                                @endif
                                            </span>
                                            <span class="text-gray-900 font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-2 pt-2 border-t border-gray-200 flex justify-between">
                                        <span class="font-bold text-gray-900">Total:</span>
                                        <span class="font-bold text-[#7b2cbf]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Project Details -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-[#7b2cbf] mr-2"></i> Detail Project
                    </h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Project <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="project_name" value="{{ old('project_name') }}" required
                                   class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                                   placeholder="Masukkan nama project">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Project
                            </label>
                            <textarea name="description" rows="4"
                                      class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                                      placeholder="Deskripsi detail tentang project ini...">{{ old('description') }}</textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Target Selesai
                                </label>
                                <input type="date" name="end_date" value="{{ old('end_date') }}"
                                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Budget Allocation -->
                <div class="bg-white rounded-xl shadow-sm p-6" id="serviceBudgetSection" style="display: none;">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-coins text-[#7b2cbf] mr-2"></i> Alokasi Budget per Layanan
                    </h2>
                    
                    <div id="serviceBudgetList" class="space-y-3">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-700">Total Budget:</span>
                            <span class="text-xl font-bold text-[#7b2cbf]" id="totalBudgetDisplay">Rp 0</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Alokasi budget akan disesuaikan dengan harga layanan yang dipilih
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary -->
            <div class="space-y-6">
                <!-- Project Summary -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-sm p-6 text-white sticky top-6">
                    <h3 class="text-lg font-bold mb-4">
                        <i class="fas fa-clipboard-check mr-2"></i> Ringkasan Project
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <p class="text-purple-100 text-xs mb-1">Order Number</p>
                            <p class="font-bold" id="summaryOrderNumber">-</p>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <p class="text-purple-100 text-xs mb-1">Client</p>
                            <p class="font-bold" id="summaryClient">-</p>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <p class="text-purple-100 text-xs mb-1">Total Budget</p>
                            <p class="font-bold text-xl" id="summaryBudget">-</p>
                        </div>
                        
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3">
                            <p class="text-purple-100 text-xs mb-1">Layanan</p>
                            <div id="summaryServices" class="text-sm">
                                <p class="text-purple-100 italic">Pilih order terlebih dahulu</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full mt-6 bg-white text-purple-600 px-6 py-3 rounded-lg font-bold hover:bg-gray-100 transition shadow-lg"
                            id="submitButton" disabled>
                        <i class="fas fa-plus-circle mr-2"></i> Buat Project
                    </button>
                </div>

                <!-- Help Tips -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h4 class="font-semibold text-blue-900 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i> Tips
                    </h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Pilih order yang akan dibuatkan project</li>
                        <li>• Berikan nama project yang jelas</li>
                        <li>• Tentukan timeline yang realistis</li>
                        <li>• Budget otomatis dari total order</li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

<script>
const ordersData = @json($orders);
let selectedOrder = null;

function selectOrder(orderId) {
    selectedOrder = ordersData.find(o => o.id === orderId);
    
    if (selectedOrder) {
        // Update summary
        document.getElementById('summaryOrderNumber').textContent = '#' + selectedOrder.order_number;
        document.getElementById('summaryClient').textContent = selectedOrder.client.user?.name || selectedOrder.client.name;
        document.getElementById('summaryBudget').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(selectedOrder.total_amount);
        
        // Update services list in summary
        let servicesHtml = '<ul class="space-y-1">';
        selectedOrder.items.forEach(item => {
            servicesHtml += `<li class="flex items-start">
                <i class="fas fa-check text-green-300 mr-2 mt-0.5"></i>
                <span class="text-sm">${item.service.name}</span>
            </li>`;
        });
        servicesHtml += '</ul>';
        document.getElementById('summaryServices').innerHTML = servicesHtml;
        
        // Show and populate service budget allocation
        const budgetSection = document.getElementById('serviceBudgetSection');
        const budgetList = document.getElementById('serviceBudgetList');
        
        budgetSection.style.display = 'block';
        
        let html = '';
        selectedOrder.items.forEach((item, index) => {
            html += `
                <div class="border-2 border-gray-200 rounded-lg p-4">
                    <input type="hidden" name="services[]" value="${item.service.id}">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">${item.service.name}</p>
                            ${item.package ? `<p class="text-xs text-gray-500">Paket: ${item.package.name}</p>` : ''}
                        </div>
                        <span class="text-sm font-bold text-[#7b2cbf]">Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Alokasi Budget</label>
                        <input type="text" 
                               name="service_budgets_display[]" 
                               value="${new Intl.NumberFormat('id-ID').format(item.subtotal)}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] text-sm budget-input"
                               onchange="updateBudgetValue(this)"
                               oninput="formatBudgetInput(this)">
                        <input type="hidden" name="service_budgets[]" value="${item.subtotal}" class="budget-hidden">
                    </div>
                </div>
            `;
        });
        
        budgetList.innerHTML = html;
        updateTotalBudget();
        
        // Enable submit button
        document.getElementById('submitButton').disabled = false;
        
        // Scroll to project details
        document.querySelector('input[name="project_name"]').focus();
    }
}

function updateTotalBudget() {
    const budgets = document.querySelectorAll('input.budget-hidden');
    let total = 0;
    
    budgets.forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    
    document.getElementById('totalBudgetDisplay').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
}

function formatBudgetInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(value);
        // Update hidden input
        const hiddenInput = input.parentElement.querySelector('.budget-hidden');
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    } else {
        input.value = '';
    }
    updateTotalBudget();
}

function updateBudgetValue(input) {
    updateTotalBudget();
}

// If there's old input (validation error), reselect the order
@if(old('order_id'))
    selectOrder({{ old('order_id') }});
@endif
</script>
@endsection
