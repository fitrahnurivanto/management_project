@extends('layouts.app')

@section('page-title', 'Ajukan Payment Request')

@section('content')
<div class="mb-6">
        <a href="{{ route('employee.payment-requests.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ajukan Permintaan Pembayaran</h2>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <form action="{{ route('employee.payment-requests.store') }}" method="POST" id="paymentRequestForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Project / Kelas <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="typeSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">Pilih Tipe...</option>
                                <option value="project">Project (Agency)</option>
                                <option value="class">Kelas (Academy)</option>
                            </select>
                        </div>

                        <div class="mb-6" id="projectSelectContainer" style="display: none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Project <span class="text-red-500">*</span>
                            </label>
                            <select name="project_id" id="projectSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                        {{ $project->project_name }} ({{ $project->project_code }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-600 mt-1">Pilih project yang ingin Anda ajukan pembayaran</p>
                        </div>

                        <div class="mb-6" id="classSelectContainer" style="display: none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Kelas <span class="text-red-500">*</span>
                            </label>
                            <select name="class_id" id="classSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Pilih Kelas...</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-600 mt-1">Pilih kelas yang ingin Anda ajukan pembayaran</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Nominal yang Diajukan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" 
                                       name="requested_amount" 
                                       class="w-full pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                       value="{{ old('requested_amount') }}"
                                       placeholder="0"
                                       min="0"
                                       step="1000"
                                       required>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">Masukkan nominal pembayaran yang ingin diajukan</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Jam Kerja (opsional)
                            </label>
                            <input type="number" 
                                   name="hours_worked" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   value="{{ old('hours_worked') }}"
                                   placeholder="0"
                                   min="0"
                                   step="0.5">
                            <p class="text-sm text-gray-600 mt-1">Jumlah jam kerja untuk project/kelas ini</p>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Catatan (opsional)
                            </label>
                            <textarea name="notes" 
                                      rows="4" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Tambahkan catatan atau keterangan tambahan...">{{ old('notes') }}</textarea>
                            <p class="text-sm text-gray-600 mt-1">Jelaskan alasan pengajuan atau detail pekerjaan yang telah diselesaikan</p>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                            </button>
                            <a href="{{ route('employee.payment-requests.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-semibold transition">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Panduan
                </h3>
                <div class="space-y-3 text-sm text-blue-900">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                        <p>Pilih project atau kelas yang sudah Anda kerjakan</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                        <p>Masukkan nominal sesuai kesepakatan</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                        <p>Tambahkan catatan untuk memperjelas pengajuan</p>
                    </div>
                    <div class="flex items-start gap-2">
                        <i class="fas fa-check-circle text-blue-600 mt-1"></i>
                        <p>Admin akan review dan approve permintaan Anda</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                <h3 class="text-lg font-semibold text-yellow-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    Perhatian
                </h3>
                <div class="space-y-2 text-sm text-yellow-900">
                    <p>• Pastikan data yang diisi sudah benar</p>
                    <p>• Nominal yang diajukan sesuai dengan pekerjaan yang telah diselesaikan</p>
                    <p>• Pengajuan yang tidak valid dapat ditolak</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('typeSelect');
    const projectContainer = document.getElementById('projectSelectContainer');
    const classContainer = document.getElementById('classSelectContainer');
    const projectSelect = document.getElementById('projectSelect');
    const classSelect = document.getElementById('classSelect');

    typeSelect.addEventListener('change', function() {
        const type = this.value;
        
        if (type === 'project') {
            projectContainer.style.display = 'block';
            classContainer.style.display = 'none';
            projectSelect.required = true;
            classSelect.required = false;
            classSelect.value = '';
        } else if (type === 'class') {
            projectContainer.style.display = 'none';
            classContainer.style.display = 'block';
            projectSelect.required = false;
            classSelect.required = true;
            projectSelect.value = '';
        } else {
            projectContainer.style.display = 'none';
            classContainer.style.display = 'none';
            projectSelect.required = false;
            classSelect.required = false;
        }
    });
</script>
@endpush
@endsection
