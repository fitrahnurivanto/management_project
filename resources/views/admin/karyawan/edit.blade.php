@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.karyawan.index') }}" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Karyawan</h1>
            <p class="text-gray-600">Update data karyawan {{ $karyawan->name }}</p>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
        <p class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Mohon perbaiki kesalahan berikut:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="max-w-2xl">
        <form action="{{ route('admin.karyawan.update', $karyawan) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Foto Profil
                    </label>
                    <div class="flex items-start gap-4">
                        <div id="preview" class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg overflow-hidden border-4 border-white ring-2 ring-purple-200">
                            @if($karyawan->avatar)
                            <img src="{{ Storage::url($karyawan->avatar) }}" alt="{{ $karyawan->name }}" class="w-full h-full object-cover">
                            @else
                            <span>{{ strtoupper(substr($karyawan->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/jpg,image/png" 
                                   class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB</p>
                            <p class="text-xs text-purple-600 mt-1"><i class="fas fa-info-circle mr-1"></i>Foto akan otomatis di-crop dan di-resize menjadi 400x400px</p>
                            @if($karyawan->avatar)
                            <p class="text-xs text-green-600 mt-1"><i class="fas fa-check-circle mr-1"></i>Foto sudah diupload</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $karyawan->name) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="Masukkan nama lengkap karyawan">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $karyawan->email) }}" required
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="email@example.com">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        No. Telepon
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $karyawan->phone) }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="08123456789">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Posisi/Jabatan
                    </label>
                    <input type="text" name="address" value="{{ old('address', $karyawan->address) }}"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="Misal: Content Creator, CS Admin, dll">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Divisi <span class="text-red-500">*</span>
                    </label>
                    <select name="division" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition">
                        <option value="">-- Pilih Divisi --</option>
                        <option value="agency" {{ old('division', $karyawan->division) == 'agency' ? 'selected' : '' }}>Agency</option>
                        <option value="academy" {{ old('division', $karyawan->division) == 'academy' ? 'selected' : '' }}>Academy</option>
                    </select>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-sm text-yellow-800 mb-2"><i class="fas fa-info-circle mr-2"></i><strong>Update Password</strong></p>
                    <p class="text-xs text-yellow-700">Kosongkan jika tidak ingin mengubah password</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Password Baru
                    </label>
                    <input type="password" name="password"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="Minimal 8 karakter (kosongkan jika tidak diubah)">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-[#7b2cbf] focus:border-[#7b2cbf] transition"
                           placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.karyawan.index') }}" 
                   class="flex-1 text-center px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    <i class="fas fa-times mr-2"></i>
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-[#7b2cbf] to-[#9d4edd] text-white rounded-lg hover:shadow-lg hover:-translate-y-0.5 transition-all font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Update Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
