@props(['order', 'presenter'])

<div class="flex flex-col space-y-2">
    <!-- WhatsApp Button (Always visible) -->
    <a href="{{ $presenter->getWhatsAppLink() }}" 
       target="_blank" 
       class="text-center text-green-600 hover:text-green-800 font-medium px-3 py-1.5 border border-green-600 rounded hover:bg-green-50 transition">
        <i class="fab fa-whatsapp mr-1"></i> Chat WA
    </a>
    
    @if($presenter->needsReview())
        <!-- Approve Button -->
        <form action="{{ route('admin.orders.confirm', $order) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full text-center text-green-600 hover:text-green-800 font-medium px-3 py-1 border border-green-600 rounded hover:bg-green-50 transition" 
                    onclick="return confirm('Approve order ini?')">
                <i class="fas fa-check mr-1"></i> Approve
            </button>
        </form>
        
        <!-- Reject Button -->
        <form action="{{ route('admin.orders.reject', $order) }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full text-center text-red-600 hover:text-red-800 font-medium px-3 py-1 border border-red-600 rounded hover:bg-red-50 transition" 
                    onclick="return confirm('Tolak order ini?')">
                <i class="fas fa-times mr-1"></i> Tolak
            </button>
        </form>
        
        @if($order->payment_type === 'installment')
        <div class="text-xs text-blue-600 italic text-center pt-1">
            <i class="fas fa-info-circle mr-1"></i> DP bisa di-approve
        </div>
        @endif
    @else
        @if($presenter->isPaid())
        <!-- Cetak PKS Button -->
        <a href="{{ route('admin.orders.pks.form', $order) }}" 
           class="text-center text-indigo-600 hover:text-indigo-800 font-medium px-3 py-1 border border-indigo-600 rounded hover:bg-indigo-50 transition">
            <i class="fas fa-file-contract mr-1"></i> Cetak PKS
        </a>
        @endif
        
        @if($presenter->hasRemainingInstallment())
        <!-- Bayar Pelunasan Button -->
        <button onclick="showInstallmentPayment({{ $order->id }})" 
                class="text-center text-blue-600 hover:text-blue-800 font-medium px-3 py-1 border border-blue-600 rounded hover:bg-blue-50 transition">
            <i class="fas fa-wallet mr-1"></i> Bayar Pelunasan
        </button>
        @endif
    @endif
</div>
