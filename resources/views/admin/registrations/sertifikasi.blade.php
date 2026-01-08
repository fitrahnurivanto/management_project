@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-pink-50 py-8 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block p-3 bg-purple-100 rounded-full mb-4">
                <i class="fas fa-certificate text-4xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pendaftaran Sertifikasi BNSP</h1>
            <p class="text-gray-600">CMU Chickens Academy - Sertifikasi Profesional</p>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
            @endif

            <form action="{{ route('admin.registrations.sertifikasi.store') }}" method="POST">
                @csrf

                <!-- Nama Lengkap -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-purple-500 mr-2"></i>Nama Lengkap
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Masukkan nama lengkap sesuai KTP">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope text-purple-500 mr-2"></i>Email
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="contoh@email.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fab fa-whatsapp text-purple-500 mr-2"></i>Nomor WhatsApp
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asal Institusi -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-purple-500 mr-2"></i>Asal Institusi/Perusahaan
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="institution_name" value="{{ old('institution_name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Nama institusi/perusahaan/universitas">
                    @error('institution_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Alamat -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-purple-500 mr-2"></i>Alamat Lengkap
                        <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Alamat lengkap termasuk kota">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Umur -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-birthday-cake text-purple-500 mr-2"></i>Umur
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="age" value="{{ old('age') }}" required min="17" max="100"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Masukkan umur">
                    @error('age')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-1">Minimal umur 17 tahun</p>
                </div>

                <!-- Info -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-purple-500 mt-1 mr-3"></i>
                        <div class="text-sm text-purple-800">
                            <p class="font-semibold mb-1">Informasi Sertifikasi:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Pendaftaran ini <strong>GRATIS</strong></li>
                                <li>Sertifikasi diakui oleh BNSP (Badan Nasional Sertifikasi Profesi)</li>
                                <li>Proses sertifikasi membutuhkan waktu 1-2 minggu</li>
                                <li>Sertifikat berlaku secara nasional</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <a href="{{ route('admin.orders.index') }}" 
                       class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i>Daftar Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>Butuh bantuan? Hubungi kami di <strong>08xx-xxxx-xxxx</strong></p>
        </div>
    </div>
</div>
@endsection
