@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-plus-circle mr-2 text-green-600"></i>Create New Order
    </h1>
    <p class="text-gray-600 mt-2">Input data pesanan dari client (via WhatsApp)</p>
</div>

@if($errors->any())
<div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
    <div class="flex items-start">
        <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
        <div class="text-red-700 text-sm">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<form action="{{ route('admin.orders.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    
    <!-- Client Information -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-user text-indigo-600 mr-2"></i>
            Informasi Client
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Client <span class="text-red-500">*</span>
                </label>
                <input type="text" name="client_name" value="{{ old('client_name') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="Nama lengkap client">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="client_email" value="{{ old('client_email') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="email@example.com">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    No. Telepon <span class="text-red-500">*</span>
                </label>
                <input type="tel" name="client_phone" value="{{ old('client_phone') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="08xxxxxxxxxx">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Perusahaan
                </label>
                <input type="text" name="company_name" value="{{ old('company_name') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="PT. Contoh Indonesia (opsional)">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Alamat Perusahaan
            </label>
            <textarea name="company_address" rows="2"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      placeholder="Alamat lengkap perusahaan (opsional)">{{ old('company_address') }}</textarea>
        </div>
    </div>

    <!-- Service Selection -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-box-open text-purple-600 mr-2"></i>
                Pilih Layanan & Paket
            </h3>
            <a href="{{ route('admin.services.index') }}"
               class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm transition">
                <i class="fas fa-cog mr-1"></i>Kelola Layanan & Paket
            </a>
        </div>
        
        <div class="space-y-4">
            <!-- Layanan -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Layanan <span class="text-red-500">*</span>
                </label>
                <select name="service_id" id="serviceSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Paket (dinamis berdasarkan layanan) -->
            <div id="packageContainer">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Paket
                </label>
                <select name="package_id" id="packageSelect"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Pilih layanan terlebih dahulu --</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pilih paket dari layanan yang dipilih, atau kosongkan untuk custom</p>
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Deskripsi Kebutuhan
            </label>
            <textarea name="description" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                      placeholder="Detail kebutuhan client...">{{ old('description') }}</textarea>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="bg-white rounded-xl shadow-md p-6 border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
            Informasi Pembayaran
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Total Harga <span class="text-red-500">*</span>
                </label>
                <input type="text" name="total_price" id="totalPrice" value="{{ old('total_price') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                       placeholder="1.500.000">
                <p class="text-xs text-gray-500 mt-1">Harga yang sudah disepakati dengan client</p>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Tipe Pembayaran <span class="text-red-500">*</span>
                </label>
                <select name="payment_type" id="paymentType" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Pilih Tipe --</option>
                    <option value="full" {{ old('payment_type') == 'full' ? 'selected' : '' }}>Full Payment (Lunas)</option>
                    <option value="installment" {{ old('payment_type') == 'installment' ? 'selected' : '' }}>Installment (Cicilan)</option>
                </select>
            </div>
        </div>
        
        <div id="installmentContainer" class="mt-4" style="display: none;">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Jumlah Cicilan
            </label>
            <select name="installment_count"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <option value="2" {{ old('installment_count') == 2 ? 'selected' : '' }}>2x Cicilan</option>
                <option value="3" {{ old('installment_count') == 3 ? 'selected' : '' }}>3x Cicilan</option>
                <option value="4" {{ old('installment_count') == 4 ? 'selected' : '' }}>4x Cicilan</option>
            </select>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Bukti Transfer (opsional)
            </label>
            <input type="file" name="payment_proof" accept="image/*,.pdf"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <p class="text-xs text-gray-500 mt-1">Upload jika client sudah transfer DP/lunas</p>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-end space-x-4">
        <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition">
            <i class="fas fa-times mr-2"></i>Batal
        </a>
        <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
            <i class="fas fa-save mr-2"></i>Simpan Order
        </button>
    </div>
</form>

@push('scripts')
<script>
    // Service data
    const services = @json($services);
    console.log('Services loaded:', services); // Debug
    
    // Format number to Indonesian format (1.500.000)
    function formatRupiah(number) {
        // Remove any existing dots first
        const cleaned = number.toString().replace(/\./g, '');
        return cleaned.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Remove format to get plain number
    function unformatRupiah(formatted) {
        return formatted.toString().replace(/\./g, '');
    }
    
    // Handle service selection
    document.getElementById('serviceSelect').addEventListener('change', function() {
        const serviceId = this.value;
        const packageSelect = document.getElementById('packageSelect');
        const totalPriceInput = document.getElementById('totalPrice');
        
        console.log('Service ID selected:', serviceId); // Debug
        
        // Reset paket
        packageSelect.innerHTML = '<option value="">-- Pilih Paket --</option>';
        totalPriceInput.value = '';
        
        if (serviceId) {
            const service = services.find(s => s.id == serviceId);
            console.log('Service found:', service); // Debug
            console.log('Packages:', service ? service.packages : null); // Debug
            
            if (service && service.packages && service.packages.length > 0) {
                // Ada paket, tampilkan
                console.log('Adding packages to dropdown...'); // Debug
                service.packages.forEach(pkg => {
                    const option = document.createElement('option');
                    option.value = pkg.id;
                    option.textContent = `${pkg.name} - Rp ${formatRupiah(parseInt(pkg.price))}`;
                    option.dataset.price = pkg.price; // Store PLAIN number only
                    packageSelect.appendChild(option);
                    console.log('Package:', pkg.name, 'Price:', pkg.price, 'Type:', typeof pkg.price); // Debug
                });
            } else {
                // Tidak ada paket
                console.log('No packages found'); // Debug
                packageSelect.innerHTML = '<option value="">-- Layanan ini tidak memiliki paket --</option>';
            }
        } else {
            packageSelect.innerHTML = '<option value="">-- Pilih layanan terlebih dahulu --</option>';
        }
    });
    
    // Handle package selection - auto fill price
    document.getElementById('packageSelect').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const totalPriceInput = document.getElementById('totalPrice');
        
        if (selectedOption && selectedOption.dataset.price) {
            // Get price from dataset (should be plain number)
            const price = selectedOption.dataset.price;
            console.log('Selected price:', price, 'Type:', typeof price); // Debug
            
            // Convert to number and format
            const numPrice = parseInt(price);
            console.log('Converted to number:', numPrice); // Debug
            
            const formatted = formatRupiah(numPrice);
            console.log('Formatted:', formatted); // Debug
            
            totalPriceInput.value = formatted;
        }
    });
    
    // Format price input on blur (when user finishes editing)
    document.getElementById('totalPrice').addEventListener('blur', function() {
        if (this.value) {
            const plainNumber = unformatRupiah(this.value);
            if (!isNaN(plainNumber) && plainNumber !== '') {
                this.value = formatRupiah(plainNumber);
            }
        }
    });
    
    // Allow only numbers and dots while typing
    document.getElementById('totalPrice').addEventListener('keypress', function(e) {
        const char = String.fromCharCode(e.which);
        if (!/[0-9.]/.test(char)) {
            e.preventDefault();
        }
    });
    
    // Handle payment method
    document.getElementById('paymentType').addEventListener('change', function() {
        const installmentContainer = document.getElementById('installmentContainer');
        
        if (this.value === 'installment') {
            installmentContainer.style.display = 'block';
        } else {
            installmentContainer.style.display = 'none';
        }
    });
    
    // Restore on page load (for old values)
    window.addEventListener('DOMContentLoaded', function() {
        const serviceSelect = document.getElementById('serviceSelect');
        const paymentType = document.getElementById('paymentType');
        
        if (serviceSelect.value) {
            serviceSelect.dispatchEvent(new Event('change'));
            
            // Restore package selection
            const packageSelect = document.getElementById('packageSelect');
            const oldPackage = "{{ old('package_id') }}";
            if (oldPackage) {
                packageSelect.value = oldPackage;
                packageSelect.dispatchEvent(new Event('change'));
            }
        }
        
        if (paymentType.value === 'installment') {
            document.getElementById('installmentContainer').style.display = 'block';
        }
    });
</script>
@endpush

@endsection
