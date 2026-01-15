@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Order</h1>
            <p class="text-gray-600">Review dan approve order dari landing page</p>
        </div>
        @if($user->isAcademyAdmin())
        <!-- Academy Registration Buttons -->
        <div class="flex gap-2">
            <a href="{{ route('admin.registrations.magang') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition inline-flex items-center">
                <i class="fas fa-user-graduate mr-2"></i>Daftar Magang
            </a>
            <a href="{{ route('admin.registrations.sertifikasi') }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition inline-flex items-center">
                <i class="fas fa-certificate mr-2"></i>Daftar Sertifikasi
            </a>
        </div>
        @endif
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <a href="{{ route('admin.orders.index', ['year' => request('year', 'all'), 'division' => request('division')]) }}" class="px-6 py-3 border-b-2 {{ !request('status') ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-list mr-2"></i> Semua
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending_review', 'year' => request('year', 'all'), 'division' => request('division')]) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'pending_review' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-clock mr-2"></i> Perlu Review 
                    @if($pendingCount > 0)
                    <span class="ml-2 bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'paid', 'year' => request('year', 'all'), 'division' => request('division')]) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'paid' ? 'border-green-600 text-green-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-check-circle mr-2"></i> Terkonfirmasi
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'rejected', 'year' => request('year', 'all'), 'division' => request('division')]) }}" class="px-6 py-3 border-b-2 {{ request('status') == 'rejected' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-800 hover:border-gray-300' }} font-medium text-sm">
                    <i class="fas fa-times-circle mr-2"></i> Ditolak
                </a>
                
                <!-- Year Filter -->
                <div class="ml-auto flex items-center px-4 gap-3">
                    @if($user->isSuperAdmin())
                    <!-- Division Filter for Super Admin -->
                    <div class="flex items-center">
                        <label class="text-xs text-gray-600 mr-2">Divisi:</label>
                        <select onchange="window.location.href='{{ route('admin.orders.index') }}?division=' + this.value + '&year={{ request('year', 'all') }}' + '{{ request('status') ? '&status=' . request('status') : '' }}'" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ request('division') == 'all' || !request('division') ? 'selected' : '' }}>Semua Divisi</option>
                            <option value="agency" {{ request('division') == 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="academy" {{ request('division') == 'academy' ? 'selected' : '' }}>Academy</option>
                        </select>
                    </div>
                    @endif
                    
                    <!-- Year Filter for All Admins -->
                    <div class="flex items-center">
                        <label class="text-xs text-gray-600 mr-2">Tahun:</label>
                        <select onchange="window.location.href='{{ route('admin.orders.index') }}?year=' + this.value + '{{ request('status') ? '&status=' . request('status') : '' }}' + '{{ request('division') ? '&division=' . request('division') : '' }}'" class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all" {{ $selectedYear == 'all' ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </nav>
        </div>
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

    <!-- Orders Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe & Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                        @if($order->pks_number)
                        <div class="text-xs text-indigo-600 font-medium">PKS: {{ $order->pks_number }}</div>
                        @endif
                        <div class="text-xs text-gray-500">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y H:i') : $order->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $order->client->name ?: ($order->client->user->name ?? 'N/A') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            <i class="fas fa-envelope mr-1"></i>
                            {{ $order->client->email ?: ($order->client->user->email ?? '-') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            <i class="fab fa-whatsapp mr-1"></i>
                            {{ $order->client->phone ?: ($order->client->contact_phone ?? '-') }}
                        </div>
                        @if($order->client->company_name)
                        <div class="text-xs text-gray-500">
                            <i class="fas fa-building mr-1"></i> {{ $order->client->company_name }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($order->order_type === 'registration')
                            <!-- Registration Type Display -->
                            <div class="mb-2">
                                @if($order->registration_type === 'magang')
                                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-indigo-100 text-indigo-800">
                                        <i class="fas fa-user-graduate mr-1"></i>Pendaftaran Magang
                                    </span>
                                @elseif($order->registration_type === 'sertifikasi')
                                    <span class="px-3 py-1.5 text-sm font-semibold rounded-lg bg-purple-100 text-purple-800">
                                        <i class="fas fa-certificate mr-1"></i>Pendaftaran Sertifikasi BNSP
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-700 space-y-1">
                                @if($order->institution_name)
                                    <div class="text-xs">
                                        <i class="fas fa-school text-gray-400 mr-1"></i>{{ $order->institution_name }}
                                    </div>
                                @endif
                                @if($order->participant_age)
                                    <div class="text-xs">
                                        <i class="fas fa-birthday-cake text-gray-400 mr-1"></i>{{ $order->participant_age }} tahun
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- Regular Order Display -->
                            <div class="text-sm text-gray-900">
                                @foreach($order->items as $item)
                                <div class="mb-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium">{{ $item->service->name }}</span>
                                        <span class="text-gray-500">({{ $item->quantity }}x)</span>
                                        @if($user->isSuperAdmin())
                                            @if($item->service->category->division === 'agency')
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    <i class="fas fa-briefcase mr-1"></i>Agency
                                                </span>
                                            @elseif($item->service->category->division === 'academy')
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-graduation-cap mr-1"></i>Academy
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    @if($order->notes && $order->notes !== $item->service->name)
                                        <div class="text-xs text-gray-500 italic ml-4">
                                            <i class="fas fa-box mr-1"></i> {{ $order->notes }}
                                        </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @endif
                        @if($order->payment_notes && $order->payment_notes !== $order->notes)
                        <div class="text-xs text-gray-500 mt-2 italic">
                            <i class="fas fa-comment mr-1"></i> {{ Str::limit($order->payment_notes, 50) }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
                        @if($order->payment_type === 'installment')
                        <div class="text-xs text-blue-600 mt-1">
                            <i class="fas fa-wallet mr-1"></i> DP {{ $order->paid_installments }}/2
                        </div>
                        <div class="text-xs text-gray-500">
                            Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                        </div>
                        @else
                        <div class="text-xs text-green-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i> Lunas
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $order->payment_method }}</div>
                        @if($order->payment_proof)
                        <a href="{{ Storage::url($order->payment_proof) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-file-image mr-1"></i> Lihat Bukti
                        </a>
                        @else
                        <span class="text-xs text-gray-500">Belum upload</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <!-- Payment Type Badge -->
                        @if($order->payment_type === 'installment')
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mb-2">
                            <i class="fas fa-wallet mr-1"></i> DP
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mb-2">
                            <i class="fas fa-money-bill-wave mr-1"></i> Lunas
                        </span>
                        @endif
                        
                        <!-- Payment Status Badge -->
                        <div>
                            @if($order->payment_status == 'pending_review')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Perlu Review
                            </span>
                            @elseif($order->payment_status == 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Terkonfirmasi
                            </span>
                            @elseif($order->payment_status == 'refunded')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                <i class="fas fa-undo mr-1"></i> Refund
                            </span>
                            @elseif($order->payment_status == 'rejected')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Ditolak
                            </span>
                            @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @php
                            $clientPhone = $order->client->phone ?: ($order->client->contact_phone ?? '');
                            $clientName = $order->client->name ?: ($order->client->user->name ?? 'Client');
                            $cleanPhone = preg_replace('/[^0-9]/', '', $clientPhone);
                            // Convert 08xxx to 628xxx for international format
                            if (substr($cleanPhone, 0, 1) === '0') {
                                $cleanPhone = '62' . substr($cleanPhone, 1);
                            }
                            
                            // WhatsApp message based on order type
                            if ($order->order_type === 'registration') {
                                if ($order->registration_type === 'magang') {
                                    $waMessage = "Halo " . urlencode($clientName) . ", terima kasih sudah mendaftar Magang (No: " . $order->order_number . "). Tim kami akan menghubungi Anda untuk informasi lebih lanjut.";
                                } elseif ($order->registration_type === 'sertifikasi') {
                                    $waMessage = "Halo " . urlencode($clientName) . ", terima kasih sudah mendaftar Sertifikasi BNSP (No: " . $order->order_number . "). Tim kami akan menghubungi Anda untuk informasi lebih lanjut.";
                                } else {
                                    $waMessage = "Halo " . urlencode($clientName) . ", mengenai pendaftaran " . $order->order_number;
                                }
                            } else {
                                $waMessage = "Halo " . urlencode($clientName) . ", mengenai order " . $order->order_number;
                            }
                        @endphp
                        
                        @if($order->payment_status == 'pending_review')
                        <div class="flex flex-col space-y-2">
                            <!-- WhatsApp Button (Priority) -->
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank" class="text-center text-green-600 hover:text-green-800 font-medium px-3 py-1.5 border border-green-600 rounded hover:bg-green-50">
                                <i class="fab fa-whatsapp mr-1"></i> Chat WA
                            </a>
                            
                            <!-- Approve/Reject Buttons -->
                            <form action="{{ route('admin.orders.confirm', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full text-center text-green-600 hover:text-green-800 font-medium px-3 py-1 border border-green-600 rounded hover:bg-green-50" onclick="return confirm('Approve order ini?')">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.reject', $order) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="w-full text-center text-red-600 hover:text-red-800 font-medium px-3 py-1 border border-red-600 rounded hover:bg-red-50" onclick="return confirm('Tolak order ini?')">
                                    <i class="fas fa-times mr-1"></i> Tolak
                                </button>
                            </form>
                            @if($order->payment_type === 'installment')
                            <div class="text-xs text-blue-600 italic text-center pt-1">
                                <i class="fas fa-info-circle mr-1"></i> DP bisa di-approve
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="flex flex-col space-y-2">
                            <!-- WhatsApp Button -->
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMessage }}" target="_blank" class="text-center text-green-600 hover:text-green-800 font-medium px-3 py-1 border border-green-600 rounded hover:bg-green-50">
                                <i class="fab fa-whatsapp mr-1"></i> Chat WA
                            </a>
                            
                            @if($order->payment_status == 'paid')
                            <!-- Cetak PKS Button -->
                            <a href="{{ route('admin.orders.pks.form', $order) }}" class="text-center text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-50">
                                <i class="fas fa-file-contract mr-1"></i> Cetak PKS
                            </a>
                            @endif
                            
                            @if($order->payment_type === 'installment' && $order->remaining_amount > 0)
                            <button onclick="showInstallmentPayment({{ $order->id }})" class="text-center text-blue-600 hover:text-blue-800 font-medium px-3 py-1 border border-blue-600 rounded hover:bg-blue-50">
                                <i class="fas fa-wallet mr-1"></i> Bayar Pelunasan
                            </button>
                            @endif
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                        <p>Belum ada order</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<!-- Modal Update DP Payment -->
<div id="installmentModal" class="hidden fixed inset-0 bg-transparent z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(2px);">
    <div class="bg-white rounded-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 flex justify-between items-center rounded-t-2xl">
            <h3 class="text-xl font-bold text-white">
                <i class="fas fa-wallet mr-2"></i> Update Pembayaran Pelunasan DP
            </h3>
            <button onclick="closeInstallmentModal()" class="text-white hover:text-gray-200 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="installmentForm" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-4">
                <div id="orderInfo" class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded text-sm">
                    <!-- Order info will be loaded here -->
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nominal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-500">Rp</span>
                        <input type="text" id="paymentAmount" name="payment_amount" required 
                            class="w-full pl-10 pr-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0" oninput="formatCurrency(this); calculateRemaining()">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nominal yang dibayarkan client</p>
                    <input type="hidden" id="paymentAmountRaw" name="payment_amount_raw">
                </div>

                <div id="remainingInfo" class="hidden bg-purple-50 border-l-4 border-purple-500 p-4 rounded text-sm">
                    <div class="flex justify-between items-center">
                        <span class="font-medium text-gray-700">Sisa Setelah Pembayaran:</span>
                        <span class="text-lg font-bold text-purple-700" id="remainingAmount">Rp 0</span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Bukti Pembayaran Pelunasan <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="payment_proof" accept="image/*,.pdf" required 
                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau PDF. Maksimal 2MB</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Catatan (opsional)
                    </label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Catatan pembayaran..."></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end gap-3">
                <button type="button" onclick="closeInstallmentModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition">
                    <i class="fas fa-check mr-2"></i> Konfirmasi Pembayaran
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentOrderData = null;

    function showInstallmentPayment(orderId) {
        // Find order data from the page
        fetch(`/admin/orders/${orderId}/installment-info`)
            .then(response => response.json())
            .then(data => {
                currentOrderData = data;
                
                document.getElementById('orderInfo').innerHTML = `
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="font-medium">Order:</span>
                            <span>${data.order_number}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Total:</span>
                            <span>Rp ${new Intl.NumberFormat('id-ID').format(data.total_amount)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Sudah Dibayar:</span>
                            <span>DP ${data.paid_installments}/2 (DP 50% + Pelunasan 50%)</span>
                        </div>
                        <div class="flex justify-between text-red-600 font-semibold">
                            <span>Sisa Belum Lunas:</span>
                            <span>Rp ${new Intl.NumberFormat('id-ID').format(data.remaining_amount)}</span>
                        </div>
                    </div>
                `;
                
                // Reset form
                document.getElementById('paymentAmount').value = '';
                document.getElementById('paymentAmountRaw').value = '';
                document.getElementById('remainingInfo').classList.add('hidden');
                
                document.getElementById('installmentForm').action = `/admin/orders/${orderId}/update-installment`;
                document.getElementById('installmentModal').classList.remove('hidden');
            });
    }

    function formatCurrency(input) {
        // Remove non-numeric characters
        let value = input.value.replace(/[^\d]/g, '');
        
        // Store raw value
        document.getElementById('paymentAmountRaw').value = value;
        
        // Format with thousand separators
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(value);
        } else {
            input.value = '';
        }
    }

    function calculateRemaining() {
        if (!currentOrderData) return;
        
        const paymentAmount = parseInt(document.getElementById('paymentAmountRaw').value) || 0;
        const remaining = currentOrderData.remaining_amount - paymentAmount;
        
        const remainingInfo = document.getElementById('remainingInfo');
        const remainingAmountEl = document.getElementById('remainingAmount');
        
        if (paymentAmount > 0) {
            remainingInfo.classList.remove('hidden');
            
            if (remaining > 0) {
                remainingInfo.className = 'bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded text-sm';
                remainingAmountEl.className = 'text-lg font-bold text-yellow-700';
                remainingAmountEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(remaining);
            } else if (remaining === 0) {
                remainingInfo.className = 'bg-green-50 border-l-4 border-green-500 p-4 rounded text-sm';
                remainingAmountEl.className = 'text-lg font-bold text-green-700';
                remainingAmountEl.innerHTML = '<i class="fas fa-check-circle mr-2"></i>LUNAS';
            } else {
                remainingInfo.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded text-sm';
                remainingAmountEl.className = 'text-lg font-bold text-red-700';
                remainingAmountEl.textContent = 'Kelebihan: Rp ' + new Intl.NumberFormat('id-ID').format(Math.abs(remaining));
            }
        } else {
            remainingInfo.classList.add('hidden');
        }
    }

    function closeInstallmentModal() {
        document.getElementById('installmentModal').classList.add('hidden');
        currentOrderData = null;
    }
</script>
@endpush
@endsection
