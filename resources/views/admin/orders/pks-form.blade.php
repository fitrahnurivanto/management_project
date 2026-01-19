@extends('layouts.app')
@section('content')
<div class="p-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Form Data PKS</h1>
                <p class="text-gray-600">Edit data sebelum mencetak Perjanjian Kerja Sama</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('admin.orders.pks.generate', $order) }}" method="POST" enctype="multipart/form-data" id="pksForm">
                @csrf
                
                <div class="p-6 space-y-6">
                    <!-- PKS Info -->
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-l-4 border-indigo-600 p-4 rounded">
                        <h3 class="font-semibold text-gray-900 mb-2">Informasi PKS</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-semibold text-gray-900 ml-2">{{ $order->order_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="font-semibold text-gray-900 ml-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Nomor PKS -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nomor PKS <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="pks_number" value="{{ old('pks_number', $order->pks_number) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="001/PKS/CMU/I/2026">
                            <p class="text-xs text-gray-500 mt-1">Format: XXX/PKS/CMU/M/YYYY</p>
                        </div>

                        <!-- Tanggal PKS -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal PKS <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="pks_date" value="{{ old('pks_date', \Carbon\Carbon::parse($order->pks_date)->format('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Durasi Pekerjaan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Durasi Pekerjaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="duration" value="{{ old('duration', $order->duration) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="1 (satu) bulan">
                            <p class="text-xs text-gray-500 mt-1">Contoh: 1 (satu) bulan</p>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Data Pihak Pertama (Creativemu)</h3>
                        <div class="bg-gray-50 p-4 rounded-lg space-y-2 text-sm">
                            <div class="flex">
                                <span class="w-32 text-gray-600">Nama:</span>
                                <span class="font-semibold">Agus Susanto</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Jabatan:</span>
                                <span class="font-semibold">Direktur Creativemu</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 text-gray-600">Alamat:</span>
                                <span class="font-semibold">Jl. Gn. Bulu No.89, RT.34, Argorejo, Kec. Sedayu, Kabupaten Bantul, Yogyakarta 55752</span>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Data Pihak Kedua (Client)</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Nama Client -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="client_name" value="{{ old('client_name', $order->client->name ?: $order->client->company_name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <!-- Jabatan Client -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="client_position" value="{{ old('client_position', $clientPosition) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Direktur">
                            </div>

                            <!-- Alamat Client -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Alamat <span class="text-red-500">*</span>
                                </label>
                                <textarea name="client_address" rows="3" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('client_address', $order->client->company_address ?: $order->client->address) }}</textarea>
                            </div>

                            <!-- Logo Client (Opsional) -->
                            <div class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Logo Client (Opsional)
                                </label>
                                
                                @if($order->client->logo)
                                <div class="mb-3">
                                    <img src="{{ Storage::url($order->client->logo) }}" alt="Client Logo" class="h-16 border border-gray-200 rounded p-2">
                                    <p class="text-xs text-gray-500 mt-1">Logo saat ini</p>
                                </div>
                                @endif
                                
                                <input type="file" name="client_logo" accept="image/png,image/jpeg,image/jpg"
                                    class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">Jika tidak diupload, logo tidak akan muncul di PKS</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Pekerjaan</h3>
                        
                        <!-- Deskripsi Layanan -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi Pekerjaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="service_description" rows="3" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Pengelolaan Website Ads Paket Large">{{ old('service_description', $order->items->pluck('service.name')->join(', ')) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Jelaskan pekerjaan yang akan dilakukan</p>
                        </div>

                        <!-- Nominal Pembayaran -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nominal Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 font-semibold">Rp</span>
                                <input type="text" id="payment_amount_display" value="{{ number_format($order->total_amount, 0, ',', '.') }}" required
                                    oninput="formatCurrency(this)"
                                    class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <input type="hidden" name="payment_amount" id="payment_amount_raw" value="{{ $order->total_amount }}">
                            <p class="text-xs text-gray-500 mt-1">Belum termasuk PPh 23</p>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('admin.orders.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 transition font-semibold">
                        Batal
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition font-semibold">
                        <i class="fas fa-file-pdf mr-2"></i>Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function formatCurrency(input) {
    // Remove non-numeric characters
    let value = input.value.replace(/[^\d]/g, '');
    
    // Store raw value
    document.getElementById('payment_amount_raw').value = value;
    
    // Format with thousand separators
    if (value) {
        input.value = new Intl.NumberFormat('id-ID').format(value);
    } else {
        input.value = '';
    }
}
</script>
@endpush
@endsection
