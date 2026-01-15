@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Pengaturan Perusahaan</h1>
            <p class="text-gray-600">Kelola informasi perusahaan untuk dokumen PKS</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-6">
                    <!-- Logo Perusahaan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Logo Perusahaan (Creativemu)
                        </label>
                        
                        @if($settings['company_logo'])
                        <div class="mb-3">
                            <img src="{{ Storage::url($settings['company_logo']) }}" alt="Company Logo" class="h-20 border border-gray-200 rounded p-2">
                            <p class="text-xs text-gray-500 mt-1">Logo saat ini</p>
                        </div>
                        @endif
                        
                        <input type="file" name="company_logo" accept="image/png,image/jpeg,image/jpg"
                            class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Format: PNG, JPG, JPEG. Maksimal 2MB. Rekomendasi: 200x60px (transparan)</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pihak Pertama</h3>
                        
                        <!-- Nama Perusahaan -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Nama Direktur -->
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Direktur <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="company_director" value="{{ old('company_director', $settings['company_director']) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Alamat Perusahaan -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Alamat Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="company_address" rows="3" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('company_address', $settings['company_address']) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200 flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition font-semibold">
                        <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Informasi:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Logo dan informasi ini akan digunakan untuk semua dokumen PKS</li>
                        <li>Logo client dapat diupload saat edit data client</li>
                        <li>Pastikan logo berformat PNG transparan untuk hasil terbaik</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
