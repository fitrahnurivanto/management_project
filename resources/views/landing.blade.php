<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREATIVEMU - Digital Marketing & Web Services</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .brand-purple { color: #7b2cbf; }
        .bg-brand-purple { background-color: #7b2cbf; }
        .hover\:bg-brand-purple:hover { background-color: #6a25a8; }
        .border-brand-purple { border-color: #7b2cbf; }
        .bg-brand-light { background-color: #f3e5ff; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-brand-purple rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-white text-xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold brand-purple">CREATIVEMU</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20ingin%20berkonsultasi%20dan%20bertanya%20mengenai%20layanan%20digital%20marketing.%0A%0ATerima%20kasih." target="_blank" class="flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Chat WhatsApp</span>
                    </a>
                    <a href="{{ route('login') }}" class="bg-brand-purple text-white px-4 py-2 rounded-lg hover:bg-brand-purple transition font-semibold">
                        <i class="fas fa-sign-in-alt mr-1"></i> Admin Login
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-purple-900 via-purple-700 to-pink-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-6">
                <div class="inline-block w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-lightbulb text-white text-4xl"></i>
                </div>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold mb-4">Tingkatkan Bisnis Anda Bersama CREATIVEMU</h2>
            <p class="text-xl mb-8 text-purple-100">Solusi Digital Marketing, Website, SEO, dan Media Sosial untuk Bisnis Anda</p>
            <a href="#order-form" class="inline-block bg-white text-purple-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                <i class="fas fa-rocket mr-2"></i> Pesan Layanan Sekarang
            </a>
        </div>
    </section>

    <!-- Services Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-4xl font-bold brand-purple mb-3">Layanan Kami</h3>
                <p class="text-gray-600 text-lg">Solusi digital terlengkap untuk mengembangkan bisnis Anda</p>
            </div>

            @php
                $categories = $services->groupBy('category.name');
                $categoryIcons = [
                    'Marketplace' => 'shopping-cart',
                    'Sosial Media' => 'share-alt',
                    'Website' => 'globe',
                    'Academy' => 'graduation-cap'
                ];
                $categoryColors = [
                    'Marketplace' => 'blue',
                    'Sosial Media' => 'pink',
                    'Website' => 'indigo',
                    'Academy' => 'green'
                ];
            @endphp

            @foreach($categories as $categoryName => $categoryServices)
            @php
                $color = $categoryColors[$categoryName] ?? 'purple';
            @endphp
            <!-- {{ $categoryName }} Category -->
            <div class="mb-16">
                <div class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-{{ $color }}-100 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-{{ $categoryIcons[$categoryName] ?? 'star' }} text-{{ $color }}-600 text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-gray-900">{{ $categoryName }}</h4>
                        <div class="h-1 w-20 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full mt-2"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categoryServices as $service)
                    <div class="bg-white border-2 border-gray-200 rounded-xl p-6 hover:shadow-xl hover:border-purple-300 transition-all duration-300 hover:-translate-y-1 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 bg-brand-light rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="fas fa-{{ $service->icon ?? 'star' }} brand-purple text-2xl"></i>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-700 rounded-full">{{ $categoryName }}</span>
                        </div>
                        <h5 class="text-lg font-bold mb-3 text-gray-900 group-hover:text-purple-700 transition">{{ $service->name }}</h5>
                        <p class="text-gray-600 mb-4 text-sm leading-relaxed line-clamp-3">{{ $service->description }}</p>
                        
                        @if($service->packages->count() > 0)
                        <div class="mb-4 pt-4 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">{{ $service->packages->count() }} Paket Tersedia</p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Mulai dari</span>
                                <span class="text-xl font-bold brand-purple ml-1">Rp {{ number_format($service->packages->min('price'), 0, ',', '.') }}</span>
                            </p>
                        </div>
                        <button onclick="showPackages({{ $service->id }})" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition flex items-center justify-center">
                            <i class="fas fa-box-open mr-2"></i> Lihat Paket
                        </button>
                        @else
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Mulai dari</p>
                                <p class="text-xl font-bold brand-purple">Rp {{ number_format($service->base_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center group-hover:bg-purple-600 transition">
                                <i class="fas fa-arrow-right brand-purple group-hover:text-white text-sm"></i>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Order Form Section -->
    <section id="order-form" class="py-16 bg-gradient-to-b from-gray-50 to-purple-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-purple-900 to-pink-600 px-8 py-8 text-center">
                    <div class="inline-block w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-file-alt text-white text-3xl"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-white mb-2">Form Pemesanan Layanan</h3>
                    <p class="text-purple-100">Isi form di bawah atau hubungi kami via WhatsApp untuk konsultasi gratis</p>
                </div>

                <div class="p-8">
                    @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 px-4 py-4 rounded-lg mb-6 flex items-start">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-green-800">Pesanan Berhasil Dikirim!</p>
                            <p class="text-green-700 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 px-4 py-4 rounded-lg mb-6">
                        <p class="font-semibold text-red-800 mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Mohon perbaiki kesalahan berikut:</p>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('landing.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        
                        <!-- Personal Info -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">1</div>
                                <h4 class="text-xl font-bold text-gray-900">Informasi Kontak</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-11">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        placeholder="Nama Anda">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        placeholder="email@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. WhatsApp <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 text-gray-500">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                        <input type="text" name="phone" value="{{ old('phone') }}" required 
                                            class="w-full pl-11 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                            placeholder="08123456789">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Perusahaan / Brand</label>
                                    <input type="text" name="company_name" value="{{ old('company_name') }}" 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                                        placeholder="Nama Perusahaan (Opsional)">
                                </div>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">2</div>
                                <h4 class="text-xl font-bold text-gray-900">Layanan yang Dipilih</h4>
                            </div>
                            <div id="selectedServicesDisplay" class="space-y-3 pl-11 mb-4">
                                <p class="text-gray-500 italic">Pilih layanan dari katalog di atas dengan klik "Lihat Paket"</p>
                            </div>
                            <input type="hidden" name="package_selections" id="packageSelectionsInput">
                            <p class="text-xs text-gray-500 pl-11"><i class="fas fa-info-circle mr-1"></i> Klik "Lihat Paket" pada setiap layanan untuk memilih</p>
                        </div>

                        <!-- Additional Info -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">3</div>
                                <h4 class="text-xl font-bold text-gray-900">Detail Kebutuhan</h4>
                            </div>
                            <div class="pl-11">
                                <textarea name="notes" rows="5" 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition" 
                                    placeholder="Ceritakan detail kebutuhan Anda: target audiens, timeline, budget range, atau pertanyaan lainnya...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">4</div>
                                <h4 class="text-xl font-bold text-gray-900">Metode Pembayaran <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="pl-11">
                                <select name="payment_method" required 
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                    <option value="Transfer">Transfer Bank (BCA, Mandiri, BNI)</option>
                                    <option value="E-Wallet">E-Wallet (GoPay, OVO, Dana, ShopeePay)</option>
                                    <option value="Cash">Cash / Tunai</option>
                                </select>
                            </div>
                        </div>

                        <!-- Payment Type -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">5</div>
                                <h4 class="text-xl font-bold text-gray-900">Tipe Pembayaran <span class="text-red-500">*</span></h4>
                            </div>
                            <div class="pl-11 space-y-3">
                                <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-400 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="payment_type" value="full" class="mt-1 mr-3" checked onchange="toggleInstallmentInfo()">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 flex items-center">
                                            <i class="fas fa-money-bill-wave text-green-600 mr-2"></i> Bayar Lunas
                                            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Langsung Diproses</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">Pembayaran langsung 100%, proses lebih cepat</div>
                                    </div>
                                </label>
                                
                                <label class="flex items-start p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-400 transition has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50">
                                    <input type="radio" name="payment_type" value="installment" class="mt-1 mr-3" onchange="toggleInstallmentInfo()">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-900 flex items-center">
                                            <i class="fas fa-sync-alt text-blue-600 mr-2"></i> Bayar Cicilan (DP + 1x Pelunasan)
                                            <span class="ml-2 text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Fleksibel</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">DP minimal 50% saat order, sisanya sesuai kesepakatan</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div id="installmentInfo" class="hidden mt-4 pl-11 p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 rounded-xl">
                                <p class="text-sm text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-info-circle text-purple-600 mr-2 text-lg"></i>
                                    <strong>Informasi Pembayaran Cicilan:</strong>
                                </p>
                                <ul class="text-sm text-gray-700 space-y-2 ml-6">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                                        <span>Upload bukti pembayaran DP minimal <strong>50%</strong> dari total order</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                                        <span>Sisa pembayaran dapat dilunasi sesuai kesepakatan dengan admin</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-purple-600 mr-2 mt-0.5"></i>
                                        <span>Project tetap bisa diproses walaupun belum lunas</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fab fa-whatsapp text-purple-600 mr-2 mt-0.5"></i>
                                        <span>Admin akan menghubungi untuk konfirmasi jadwal pelunasan via WhatsApp</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Payment Proof -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-brand-purple rounded-full flex items-center justify-center text-white font-bold mr-3">6</div>
                                <h4 class="text-xl font-bold text-gray-900">Bukti Pembayaran <span class="text-gray-500 text-sm font-normal">(Opsional)</span></h4>
                            </div>
                            <div class="pl-11">
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                    <input type="file" name="payment_proof" accept="image/*,.pdf" 
                                        class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                                    <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, atau PDF. Maksimal 2MB</p>
                                    <p class="text-xs text-purple-600 mt-1" id="dpNote" style="display: none;">
                                        <i class="fas fa-info-circle mr-1"></i> Upload bukti DP minimal 50% jika pilih cicilan
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t-2 border-gray-200">
                            <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20tertarik%20untuk%20menggunakan%20jasa%20digital%20marketing%20dan%20ingin%20berdiskusi%20lebih%20lanjut.%0A%0AMohon%20informasi%20lebih%20detail%20mengenai%3A%0A-%20Paket%20layanan%20yang%20tersedia%0A-%20Harga%20dan%20metode%20pembayaran%0A-%20Estimasi%20waktu%20pengerjaan%0A%0ATerima%20kasih." 
                                target="_blank" 
                                class="text-green-600 hover:text-green-700 font-semibold flex items-center">
                                <i class="fab fa-whatsapp text-2xl mr-2"></i> 
                                <span>Atau konsultasi via WhatsApp</span>
                            </a>
                            <button type="submit" 
                                class="bg-gradient-to-r from-purple-700 to-pink-600 text-white px-10 py-4 rounded-xl font-bold text-lg hover:from-purple-800 hover:to-pink-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trust Badge -->
            <div class="text-center mt-8">
                <p class="text-gray-600 flex items-center justify-center">
                    <i class="fas fa-shield-alt brand-purple mr-2"></i>
                    Data Anda aman dan akan kami proses dalam 1x24 jam
                </p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-brand-purple rounded-lg flex items-center justify-center">
                            <i class="fas fa-lightbulb text-white text-xl"></i>
                        </div>
                        <h4 class="text-xl font-bold">CREATIVEMU</h4>
                    </div>
                    <p class="text-gray-400 text-sm">Solusi Digital Marketing, Website, dan Media Sosial untuk mengembangkan bisnis Anda ke level selanjutnya.</p>
                </div>
                <div>
                    <h5 class="text-lg font-bold mb-4">Layanan</h5>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><i class="fas fa-check brand-purple mr-2"></i> Digital Marketing</li>
                        <li><i class="fas fa-check brand-purple mr-2"></i> Website Development</li>
                        <li><i class="fas fa-check brand-purple mr-2"></i> SEO Optimization</li>
                        <li><i class="fas fa-check brand-purple mr-2"></i> Social Media Management</li>
                    </ul>
                </div>
                <div>
                    <h5 class="text-lg font-bold mb-4">Hubungi Kami</h5>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li>
                            <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20ingin%20mengetahui%20lebih%20lanjut%20tentang%20layanan%20yang%20tersedia.%0A%0ATerima%20kasih." class="flex items-center hover:text-green-400 transition">
                                <i class="fab fa-whatsapp text-xl mr-3"></i>
                                <span>WhatsApp: +62 896-1847-2759</span>
                            </a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-xl mr-3"></i>
                            <span>info@creativemu.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-xl mr-3"></i>
                            <span>Indonesia</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 text-center">
                <p class="text-gray-400">&copy; 2025 CREATIVEMU. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Package Selection -->
    <div id="packageModal" class="hidden fixed inset-0 bg-transparent z-50 flex items-center justify-center p-4" style="backdrop-filter: blur(2px);">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-purple-700 to-pink-600 px-6 py-4 flex justify-between items-center flex-shrink-0 rounded-t-2xl">
                <h3 class="text-2xl font-bold text-white" id="modalTitle">Pilih Paket</h3>
                <button onclick="closePackageModal()" class="text-white hover:text-gray-200 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="packageContent" class="p-6 overflow-y-auto flex-1">
                <!-- Packages will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        const servicesData = @json($services);
        let selectedPackages = {};

        function showPackages(serviceId) {
            const service = servicesData.find(s => s.id === serviceId);
            if (!service || !service.packages || service.packages.length === 0) return;

            document.getElementById('modalTitle').textContent = `Pilih Paket - ${service.name}`;
            
            let html = '<div class="grid grid-cols-1 md:grid-cols-3 gap-4">';
            
            service.packages.forEach(pkg => {
                const isSelected = selectedPackages[serviceId] === pkg.id;
                const popularBadge = pkg.is_popular ? '<span class="absolute -top-3 right-4 bg-yellow-500 text-white text-xs font-bold px-3 py-1 rounded-full">PALING LARIS</span>' : '';
                
                html += `
                    <div class="relative border-2 ${isSelected ? 'border-purple-600 bg-purple-50' : 'border-gray-200'} rounded-xl p-6 hover:shadow-lg transition cursor-pointer" onclick="selectPackage(${serviceId}, ${pkg.id}, '${pkg.name}', ${pkg.price})">
                        ${popularBadge}
                        <div class="text-center mb-4">
                            <h4 class="text-xl font-bold text-gray-900 mb-2">${pkg.name}</h4>
                            <div class="text-3xl font-bold text-purple-700 mb-1">Rp ${new Intl.NumberFormat('id-ID').format(pkg.price)}</div>
                            ${pkg.duration ? `<p class="text-sm text-gray-600">${pkg.duration} hari</p>` : ''}
                        </div>
                        <div class="space-y-2 mb-4">
                            ${pkg.features.map(f => `
                                <div class="flex items-start text-sm">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>${f}</span>
                                </div>
                            `).join('')}
                        </div>
                        <button class="w-full py-2 rounded-lg font-semibold ${isSelected ? 'bg-purple-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-purple-100'}">
                            ${isSelected ? '<i class="fas fa-check mr-2"></i>Dipilih' : 'Pilih Paket'}
                        </button>
                    </div>
                `;
            });
            
            html += '</div>';
            html += `
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="closePackageModal()" class="px-6 py-3 rounded-lg font-semibold border-2 border-gray-300 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button onclick="confirmPackageSelection()" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition">
                        <i class="fas fa-check mr-2"></i> Konfirmasi
                    </button>
                </div>
            `;
            
            document.getElementById('packageContent').innerHTML = html;
            document.getElementById('packageModal').classList.remove('hidden');
        }

        function selectPackage(serviceId, packageId, packageName, price) {
            selectedPackages[serviceId] = packageId;
            showPackages(serviceId); // Refresh modal
        }

        function confirmPackageSelection() {
            updateSelectedServicesDisplay();
            closePackageModal();
            // Scroll to order form
            document.getElementById('order-form').scrollIntoView({ behavior: 'smooth' });
        }

        function updateSelectedServicesDisplay() {
            const displayDiv = document.getElementById('selectedServicesDisplay');
            const hiddenInput = document.getElementById('packageSelectionsInput');
            
            if (Object.keys(selectedPackages).length === 0) {
                displayDiv.innerHTML = '<p class="text-gray-500 italic">Pilih layanan dari katalog di atas dengan klik "Lihat Paket"</p>';
                hiddenInput.value = '';
                return;
            }

            let html = '';
            let totalPrice = 0;
            let packageData = [];

            Object.entries(selectedPackages).forEach(([serviceId, packageId]) => {
                const service = servicesData.find(s => s.id == serviceId);
                const pkg = service.packages.find(p => p.id == packageId);
                
                if (service && pkg) {
                    packageData.push({ service_id: serviceId, package_id: packageId });
                    totalPrice += parseFloat(pkg.price);
                    
                    html += `
                        <div class="flex items-center justify-between p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">${service.name}</p>
                                <p class="text-sm text-gray-600">${pkg.name} - ${pkg.duration ? pkg.duration + ' hari' : ''}</p>
                            </div>
                            <div class="text-right mr-3">
                                <p class="text-lg font-bold text-purple-700">Rp ${new Intl.NumberFormat('id-ID').format(pkg.price)}</p>
                            </div>
                            <button type="button" onclick="removeService(${serviceId})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times-circle text-xl"></i>
                            </button>
                        </div>
                    `;
                }
            });

            html += `
                <div class="p-4 bg-purple-100 border-2 border-purple-600 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Estimasi:</span>
                        <span class="text-2xl font-bold text-purple-700">Rp ${new Intl.NumberFormat('id-ID').format(totalPrice)}</span>
                    </div>
                </div>
            `;

            displayDiv.innerHTML = html;
            hiddenInput.value = JSON.stringify(packageData);
        }

        function removeService(serviceId) {
            delete selectedPackages[serviceId];
            updateSelectedServicesDisplay();
        }

        function closePackageModal() {
            document.getElementById('packageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('packageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePackageModal();
            }
        });

        // Toggle installment info
        function toggleInstallmentInfo() {
            const installmentRadio = document.querySelector('input[name="payment_type"][value="installment"]');
            const installmentInfo = document.getElementById('installmentInfo');
            const dpNote = document.getElementById('dpNote');
            
            if (installmentRadio.checked) {
                installmentInfo.classList.remove('hidden');
                if (dpNote) dpNote.style.display = 'block';
            } else {
                installmentInfo.classList.add('hidden');
                if (dpNote) dpNote.style.display = 'none';
            }
        }
    </script>
</body>
</html>
