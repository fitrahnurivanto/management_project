<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Academy - CREATIVEMU</title>
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
<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 min-h-screen">
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
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-purple-700 font-medium transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                    </a>
                    <a href="https://wa.me/6289618472759?text=Halo%20Admin%20CREATIVEMU%2C%0A%0ASaya%20ingin%20berkonsultasi%20mengenai%20Academy.%0A%0ATerima%20kasih." target="_blank" class="flex items-center space-x-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fab fa-whatsapp text-xl"></i>
                        <span>Chat WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <div class="inline-block w-20 h-20 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-4xl"></i>
                </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Pendaftaran Academy</h2>
                <p class="text-lg text-gray-600">Tingkatkan skill digital marketing Anda bersama CREATIVEMU Academy</p>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-8 text-center">
                    <h3 class="text-2xl font-bold text-white mb-2">Form Pendaftaran Peserta</h3>
                    <p class="text-green-100">Lengkapi data diri Anda untuk mendaftar program academy</p>
                </div>

                <div class="p-8">
                    @if(session('success'))
                    <div class="bg-green-50 border-l-4 border-green-500 px-4 py-4 rounded-lg mb-6 flex items-start">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3 mt-0.5"></i>
                        <div>
                            <p class="font-semibold text-green-800">Pendaftaran Berhasil!</p>
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

                    <form action="{{ route('academy.register.submit') }}" method="POST" class="space-y-8">
                        @csrf
                        
                        <!-- Data Pribadi -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold mr-3">1</div>
                                <h4 class="text-xl font-bold text-gray-900">Data Pribadi</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-11">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="Nama lengkap Anda">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="email" value="{{ old('email') }}" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="email@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">No. WhatsApp <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-3.5 text-gray-500">
                                            <i class="fab fa-whatsapp"></i>
                                        </span>
                                        <input type="text" name="phone" value="{{ old('phone') }}" required 
                                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                            placeholder="08xxxxxxxxxx">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Usia <span class="text-red-500">*</span></label>
                                    <input type="number" name="age" value="{{ old('age') }}" required min="15" max="100"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="Usia Anda">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap <span class="text-red-500">*</span></label>
                                    <textarea name="address" required rows="3"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="Alamat lengkap Anda">{{ old('address') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Pendidikan & Pekerjaan -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold mr-3">2</div>
                                <h4 class="text-xl font-bold text-gray-900">Pendidikan & Pekerjaan</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pl-11">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                                    <select name="education" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                        <option value="">Pilih Pendidikan</option>
                                        <option value="SMA/SMK" {{ old('education') == 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                        <option value="D3" {{ old('education') == 'D3' ? 'selected' : '' }}>D3</option>
                                        <option value="S1" {{ old('education') == 'S1' ? 'selected' : '' }}>S1</option>
                                        <option value="S2" {{ old('education') == 'S2' ? 'selected' : '' }}>S2</option>
                                        <option value="S3" {{ old('education') == 'S3' ? 'selected' : '' }}>S3</option>
                                        <option value="Lainnya" {{ old('education') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status Pekerjaan <span class="text-red-500">*</span></label>
                                    <select name="employment_status" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                        <option value="">Pilih Status</option>
                                        <option value="Pelajar/Mahasiswa" {{ old('employment_status') == 'Pelajar/Mahasiswa' ? 'selected' : '' }}>Pelajar/Mahasiswa</option>
                                        <option value="Karyawan" {{ old('employment_status') == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                                        <option value="Wiraswasta" {{ old('employment_status') == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                                        <option value="Freelancer" {{ old('employment_status') == 'Freelancer' ? 'selected' : '' }}>Freelancer</option>
                                        <option value="Tidak Bekerja" {{ old('employment_status') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                        <option value="Lainnya" {{ old('employment_status') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Program yang Dipilih -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold mr-3">3</div>
                                <h4 class="text-xl font-bold text-gray-900">Program Academy</h4>
                            </div>
                            <div class="grid grid-cols-1 gap-4 pl-11">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Program <span class="text-red-500">*</span></label>
                                    <select name="program" required 
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                                        <option value="">Pilih Program</option>
                                        @foreach($academyServices as $service)
                                        <option value="{{ $service->id }}" {{ old('program') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Motivasi Mengikuti Program <span class="text-red-500">*</span></label>
                                    <textarea name="motivation" required rows="4"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="Ceritakan motivasi Anda mengikuti program academy ini...">{{ old('motivation') }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pengalaman Digital Marketing (Opsional)</label>
                                    <textarea name="experience" rows="3"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                        placeholder="Ceritakan pengalaman Anda di bidang digital marketing (jika ada)...">{{ old('experience') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                            <button type="submit" 
                                class="px-8 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold text-lg hover:from-green-700 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="mt-8 bg-blue-50 border-2 border-blue-200 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h5 class="text-lg font-bold text-blue-900 mb-2">Informasi Penting</h5>
                        <ul class="text-blue-800 text-sm space-y-1">
                            <li><i class="fas fa-check-circle mr-2"></i>Pendaftaran akan diproses maksimal 2x24 jam</li>
                            <li><i class="fas fa-check-circle mr-2"></i>Anda akan dihubungi via WhatsApp untuk konfirmasi</li>
                            <li><i class="fas fa-check-circle mr-2"></i>Konsultasi gratis tersedia via WhatsApp</li>
                            <li><i class="fas fa-check-circle mr-2"></i>Dapatkan sertifikat setelah menyelesaikan program</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-600 text-sm">© 2025 CREATIVEMU. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
