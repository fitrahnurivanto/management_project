<!-- Payment Request Modal (Employee Only) -->
<div id="paymentRequestModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900">Ajukan Payment Request</h3>
                <button onclick="closePaymentRequestModal()" 
                        class="text-gray-400 hover:text-gray-600"
                        aria-label="Tutup modal">
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
                    <label for="project_name" class="block text-sm font-semibold text-gray-700 mb-2">Project</label>
                    <input type="text" 
                           id="project_name"
                           value="{{ $project->project_name }}" 
                           disabled 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                
                <div>
                    <label for="payment_amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nominal Payment <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold" aria-hidden="true">Rp</span>
                        <input type="text" 
                               id="payment_amount" 
                               oninput="formatCurrencyInput(this)" 
                               required
                               class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="0"
                               aria-label="Masukkan nominal payment request">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Masukkan nominal yang ingin diajukan</p>
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Catatan (Optional)</label>
                    <textarea name="notes" 
                              id="notes"
                              rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Keterangan pekerjaan yang sudah diselesaikan..."></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-200 flex gap-3">
                <button type="button" 
                        onclick="closePaymentRequestModal()"
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

@push('scripts')
<script>
function openPaymentRequestModal() {
    document.getElementById('paymentRequestModal').classList.remove('hidden');
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

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Close modal when clicking outside
document.getElementById('paymentRequestModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentRequestModal();
    }
});
</script>
@endpush
