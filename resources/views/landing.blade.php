<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CREATIVEMU - Digital Marketing & Web Services</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .brand-purple { color: #7b2cbf; }
        .bg-brand-purple { background-color: #7b2cbf; }
        .hover\:bg-brand-purple:hover { background-color: #6a25a8; }
        .border-brand-purple { border-color: #7b2cbf; }
        .bg-brand-light { background-color: #f3e5ff; }
        
        @keyframes shrink {
            from { width: 100%; }
            to { width: 0%; }
        }
        .animate-shrink {
            animation: shrink 5s linear forwards;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header - Responsive -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-brand-purple rounded-lg flex items-center justify-center">
                        <i class="fas fa-lightbulb text-white text-lg sm:text-xl"></i>
                    </div>
                    <h1 class="text-lg sm:text-2xl font-bold brand-purple">CREATIVEMU</h1>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20ingin%20berkonsultasi%20dan%20bertanya%20mengenai%20layanan%20digital%20marketing.%0A%0ATerima%20kasih." target="_blank" class="flex items-center space-x-1 sm:space-x-2 bg-green-500 hover:bg-green-600 text-white px-3 py-2 sm:px-4 rounded-lg transition text-sm sm:text-base">
                        <i class="fab fa-whatsapp text-lg sm:text-xl"></i>
                        <span class="hidden sm:inline">Chat WhatsApp</span>
                        <span class="sm:hidden">Chat</span>
                    </a>
                    <a href="{{ route('login') }}" class="bg-brand-purple text-white px-3 py-2 sm:px-4 rounded-lg hover:bg-brand-purple transition font-semibold text-sm sm:text-base">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        <span class="hidden sm:inline">Admin Login</span>
                        <span class="sm:hidden">Login</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section - Responsive -->
    <section class="bg-gradient-to-r from-purple-900 via-purple-700 to-pink-600 text-white py-12 sm:py-16 lg:py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="mb-4 sm:mb-6">
                <div class="inline-block w-16 h-16 sm:w-20 sm:h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mb-3 sm:mb-4">
                    <i class="fas fa-lightbulb text-white text-3xl sm:text-4xl"></i>
                </div>
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 px-2">Tingkatkan Bisnis Anda Bersama CREATIVEMU</h2>
            <p class="text-base sm:text-lg lg:text-xl mb-6 sm:mb-8 text-purple-100 px-4 sm:px-0">Solusi Digital Marketing, Website, SEO, dan Media Sosial untuk Bisnis Anda</p>
            <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20tertarik%20dengan%20layanan%20digital%20marketing%20Anda.%0A%0AMohon%20informasi%20lebih%20lanjut.%0A%0ATerima%20kasih." target="_blank" class="inline-flex items-center justify-center bg-white text-purple-700 px-6 sm:px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg text-sm sm:text-base">
                <i class="fab fa-whatsapp mr-2"></i>
                <span class="hidden xs:inline">Hubungi Kami via WhatsApp</span>
                <span class="xs:hidden">Hubungi via WhatsApp</span>
            </a>
        </div>
    </section>

    <!-- Services Section - Responsive -->
    <section class="py-8 sm:py-12 lg:py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 lg:mb-16">
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold brand-purple mb-2 sm:mb-3">Layanan Kami</h3>
                <p class="text-gray-600 text-sm sm:text-base lg:text-lg px-4">Solusi digital terlengkap untuk mengembangkan bisnis Anda</p>
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
            <!-- {{ $categoryName }} Category - Responsive -->
            <div class="mb-8 sm:mb-12 lg:mb-16">
                <div class="flex items-center justify-between mb-4 sm:mb-6 lg:mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-{{ $color }}-100 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                            <i class="fas fa-{{ $categoryIcons[$categoryName] ?? 'star' }} text-{{ $color }}-600 text-lg sm:text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900">{{ $categoryName }}</h4>
                            <div class="h-1 w-12 sm:w-20 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full mt-1 sm:mt-2"></div>
                        </div>
                    </div>
                    
                  
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
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
                        @if($categoryName === 'Academy')
                        <button onclick="showPackages({{ $service->id }})" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition flex items-center justify-center">
                            <i class="fas fa-box-open mr-2"></i> Pilih Paket
                        </button>
                        @else
                        <button onclick="showPackages({{ $service->id }})" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition flex items-center justify-center">
                            <i class="fas fa-box-open mr-2"></i> Lihat Paket
                        </button>
                        @endif
                        @else
                        <div class="pt-4 border-t border-gray-200">
                            <div class="mb-3">
                                <p class="text-xs text-gray-500 mb-1">Mulai dari</p>
                                <p class="text-xl font-bold brand-purple">Rp {{ number_format($service->base_price, 0, ',', '.') }}</p>
                            </div>
                            @if($categoryName === 'Academy')
                            <button onclick="selectService({{ $service->id }})" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 transition flex items-center justify-center">
                                <i class="fas fa-user-graduate mr-2"></i> Daftar Sekarang
                            </button>
                            @else
                            <button onclick="selectService({{ $service->id }})" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition flex items-center justify-center">
                                <i class="fas fa-shopping-cart mr-2"></i> Pesan Sekarang
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Footer - Responsive -->
    <footer class="bg-gray-900 text-white py-8 sm:py-10 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-6 sm:mb-8">
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

    <!-- Success Toast Notification -->
    <div id="successToast" class="hidden fixed top-4 right-4 z-[60] bg-white rounded-xl shadow-2xl border-2 border-green-500 overflow-hidden transform transition-all duration-500">
        <div class="flex items-start p-4 gap-3 max-w-md">
            <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-gray-900 mb-1">Berhasil!</h4>
                <p id="toastMessage" class="text-sm text-gray-600"></p>
            </div>
            <button onclick="closeToast()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="h-1 bg-green-500 animate-shrink"></div>
    </div>

    <!-- Modal Package Selection -->
    <div id="packageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
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

    <!-- Modal Order Form -->
    <div id="orderModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-purple-700 to-pink-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-white">Form Pemesanan</h3>
                <button onclick="closeOrderModal()" class="text-white hover:text-gray-200 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto flex-1" id="orderFormContent">
                <!-- Order form will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Modal Magang Registration -->
    <div id="magangModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-700 to-purple-600 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-user-graduate mr-3"></i>Pendaftaran Magang
                    </h3>
                    <p class="text-indigo-100 text-sm mt-1">Program Magang dan Prakerin SMK</p>
                </div>
                <button onclick="closeMagangModal()" class="text-white hover:text-gray-200 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="magangForm" class="p-6 overflow-y-auto flex-1 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-indigo-500 mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Masukkan nama lengkap">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-500 mr-2"></i>Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="contoh@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-whatsapp text-indigo-500 mr-2"></i>WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-school text-indigo-500 mr-2"></i>Asal Sekolah/SMK <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="institution_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Nama sekolah/SMK">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-indigo-500 mr-2"></i>Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="2" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Alamat lengkap termasuk kota"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake text-indigo-500 mr-2"></i>Umur <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="age" required min="15" max="25"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                           placeholder="Minimal 15 tahun">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment text-indigo-500 mr-2"></i>Catatan (Opsional)
                    </label>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                              placeholder="Minat jurusan, pertanyaan, dll"></textarea>
                </div>
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-3 text-sm text-indigo-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Pendaftaran GRATIS</strong> - Durasi minimal 3 bulan, mendapat sertifikat
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeMagangModal()" 
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700">
                        <i class="fas fa-paper-plane mr-2"></i>Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Sertifikasi Registration -->
    <div id="sertifikasiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] flex flex-col overflow-hidden">
            <div class="bg-gradient-to-r from-purple-700 to-pink-600 px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-certificate mr-3"></i>Pendaftaran Sertifikasi BNSP
                    </h3>
                    <p class="text-purple-100 text-sm mt-1">Sertifikasi Profesional</p>
                </div>
                <button onclick="closeSertifikasiModal()" class="text-white hover:text-gray-200 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="sertifikasiForm" class="p-6 overflow-y-auto flex-1 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-purple-500 mr-2"></i>Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="Masukkan nama lengkap sesuai KTP">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-purple-500 mr-2"></i>Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="contoh@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fab fa-whatsapp text-purple-500 mr-2"></i>WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" required
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                               placeholder="08xxxxxxxxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-purple-500 mr-2"></i>Asal Institusi/Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="institution_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="Nama institusi/perusahaan/universitas">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-purple-500 mr-2"></i>Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="2" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              placeholder="Alamat lengkap termasuk kota"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake text-purple-500 mr-2"></i>Umur <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="age" required min="17" max="100"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                           placeholder="Minimal 17 tahun">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-comment text-purple-500 mr-2"></i>Catatan (Opsional)
                    </label>
                    <textarea name="notes" rows="2"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                              placeholder="Pertanyaan atau informasi tambahan"></textarea>
                </div>
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-sm text-purple-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Pendaftaran GRATIS</strong> - Sertifikasi BNSP, proses 1-2 minggu, berlaku nasional
                </div>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeSertifikasiModal()" 
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700">
                        <i class="fas fa-paper-plane mr-2"></i>Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pass services data to global scope for landing-popups.js -->
    <script>
        window.servicesData = @json($services);
    </script>
    <script src="{{ asset('js/landing-popups.js') }}"></script>
    
    <!-- Check for success message -->
    @if(session('success'))
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                showToast('{{ session("success") }}');
            });
        </script>
    @endif
</body>
</html>
