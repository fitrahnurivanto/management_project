@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.classes.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ isset($clas) ? 'Edit Kelas' : 'Tambah Kelas' }}
                    </h1>
                    <p class="text-gray-600">Lengkapi informasi kelas di bawah ini</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{ isset($clas) ? route('admin.classes.update', $clas) : route('admin.classes.store') }}" 
                  method="POST">
                @csrf
                @if(isset($clas))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kelas -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $clas->name ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('name') border-red-500 @enderror"
                               placeholder="Contoh: Laravel Advanced Development"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Trainer -->
                    <div class="md:col-span-2">
                        <label for="trainer" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Trainer <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="trainer" 
                               id="trainer" 
                               value="{{ old('trainer', $clas->trainer ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('trainer') border-red-500 @enderror"
                               placeholder="Contoh: John Doe"
                               required>
                        @error('trainer')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Harga per Siswa -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga per Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price" 
                               value="{{ old('price', $clas->price ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('price') border-red-500 @enderror"
                               placeholder="0"
                               min="0"
                               step="0.01"
                               required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Siswa -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="amount" 
                               id="amount" 
                               value="{{ old('amount', $clas->amount ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('amount') border-red-500 @enderror"
                               placeholder="0"
                               min="1"
                               required>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Biaya Operasional -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Biaya Operasional <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="cost" 
                               id="cost" 
                               value="{{ old('cost', $clas->cost ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('cost') border-red-500 @enderror"
                               placeholder="0"
                               min="0"
                               step="0.01"
                               required>
                        @error('cost')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Pertemuan -->
                    <div>
                        <label for="meet" class="block text-sm font-medium text-gray-700 mb-2">
                            Jumlah Pertemuan <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="meet" 
                               id="meet" 
                               value="{{ old('meet', $clas->meet ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('meet') border-red-500 @enderror"
                               placeholder="0"
                               min="1"
                               required>
                        @error('meet')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Durasi -->
                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Durasi per Pertemuan (menit) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="duration" 
                               id="duration" 
                               value="{{ old('duration', $clas->duration ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('duration') border-red-500 @enderror"
                               placeholder="0"
                               min="1"
                               required>
                        @error('duration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metode -->
                    <div>
                        <label for="method" class="block text-sm font-medium text-gray-700 mb-2">
                            Metode Pembelajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="method" 
                                id="method" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('method') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Metode</option>
                            <option value="online" {{ old('method', $clas->method ?? '') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="offline" {{ old('method', $clas->method ?? '') == 'offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                        @error('method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" 
                                id="status" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('status') border-red-500 @enderror"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="pending" {{ old('status', $clas->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ old('status', $clas->status ?? '') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ old('status', $clas->status ?? '') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Mulai -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="start_date" 
                               id="start_date" 
                               value="{{ old('start_date', isset($clas) ? $clas->start_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('start_date') border-red-500 @enderror"
                               required>
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="end_date" 
                               id="end_date" 
                               value="{{ old('end_date', isset($clas) ? $clas->end_date->format('Y-m-d') : '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('end_date') border-red-500 @enderror"
                               required>
                        @error('end_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#7b2cbf] focus:border-[#7b2cbf] @error('description') border-red-500 @enderror"
                                  placeholder="Deskripsi kelas...">{{ old('description', $clas->description ?? '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.classes.index') }}" 
                       class="flex-1 px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            class="flex-1 px-6 py-2.5 bg-gradient-to-r from-[#7b2cbf] to-[#9d4edd] text-white rounded-lg hover:shadow-lg transition">
                        {{ isset($clas) ? 'Update Kelas' : 'Simpan Kelas' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
