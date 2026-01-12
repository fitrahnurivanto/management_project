@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.payment-requests.index') }}" class="text-gray-600 hover:text-gray-900 transition">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Review Permintaan Pembayaran</h1>
                <p class="text-gray-600">Detail dan approval payment request</p>
            </div>
        </div>
        
        @if($paymentRequest->status === 'pending')
            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full flex items-center gap-2">
                <i class="fas fa-clock"></i> Menunggu Review
            </span>
        @elseif($paymentRequest->status === 'approved')
            <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full flex items-center gap-2">
                <i class="fas fa-check-circle"></i> Telah Disetujui
            </span>
        @else
            <span class="px-4 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded-full flex items-center gap-2">
                <i class="fas fa-times-circle"></i> Ditolak
            </span>
        @endif
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Employee & Project Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Informasi Permintaan</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Employee Info -->
                    <div class="flex items-start gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-blue-700 font-medium mb-1">Employee</p>
                            <p class="text-lg font-bold text-gray-900">{{ $paymentRequest->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $paymentRequest->user->email }}</p>
                        </div>
                    </div>

                    <!-- Project Info -->
                    <div class="flex items-start gap-4 p-4 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-project-diagram text-white text-lg"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-purple-700 font-medium mb-1">Project</p>
                            <p class="text-lg font-bold text-gray-900">{{ $paymentRequest->project->project_name }}</p>
                            <p class="text-sm text-gray-600">{{ $paymentRequest->project->project_code }}</p>
                        </div>
                    </div>

                    <!-- Amount Requested -->
                    <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl border-2 border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-700 font-medium mb-1">Nominal Diajukan</p>
                                <p class="text-3xl font-bold text-green-900">
                                    Rp {{ number_format($paymentRequest->requested_amount, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="w-16 h-16 bg-green-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-money-bill-wave text-green-700 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Employee Notes -->
                    @if($paymentRequest->notes)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-comment-alt text-gray-400 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-700 mb-1">Catatan dari Employee:</p>
                                    <p class="text-gray-900">{{ $paymentRequest->notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Admin Notes (if processed) -->
                    @if($paymentRequest->admin_notes)
                        <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-user-shield text-indigo-600 mt-1"></i>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-indigo-700 mb-1">Catatan dari Admin:</p>
                                    <p class="text-gray-900">{{ $paymentRequest->admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval Form -->
            @if($paymentRequest->status === 'pending')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-600 to-purple-600">
                        <h3 class="text-lg font-bold text-white flex items-center gap-2">
                            <i class="fas fa-clipboard-check"></i>
                            Proses Permintaan
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('admin.payment-requests.update', $paymentRequest) }}" method="POST" id="approvalForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" id="actionInput">

                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nominal Disetujui <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                                    <input type="number" name="approved_amount" 
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                           value="{{ old('approved_amount', $paymentRequest->requested_amount) }}" 
                                           min="0" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle"></i> Anda dapat mengubah nominal jika diperlukan
                                </p>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Catatan Admin (Optional)</label>
                                <textarea name="admin_notes" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                                          rows="4" maxlength="1000" 
                                          placeholder="Tambahkan catatan untuk employee...">{{ old('admin_notes') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" onclick="submitForm('approve')" 
                                        class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle"></i> Setujui Pembayaran
                                </button>
                                <button type="button" onclick="submitForm('reject')" 
                                        class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold flex items-center justify-center gap-2">
                                    <i class="fas fa-times-circle"></i> Tolak Permintaan
                                </button>
                            </div>
                        </form>

<script>
function submitForm(action) {
    document.getElementById('actionInput').value = action;
    document.getElementById('approvalForm').submit();
}
</script>
                    </div>
                </div>
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 text-xl mt-1"></i>
                        <div>
                            <p class="text-blue-900 font-semibold mb-1">Permintaan Sudah Diproses</p>
                            <p class="text-blue-800">
                                Diproses pada {{ $paymentRequest->approved_at ? $paymentRequest->approved_at->format('d/m/Y H:i') : $paymentRequest->updated_at->format('d/m/Y H:i') }}
                                @if($paymentRequest->approver)
                                    oleh <span class="font-semibold">{{ $paymentRequest->approver->name }}</span>
                                @endif
                            </p>
                            @if($paymentRequest->approved_amount)
                                <p class="text-green-700 font-bold text-lg mt-2">
                                    Nominal Disetujui: Rp {{ number_format($paymentRequest->approved_amount, 0, ',', '.') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">Timeline</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Created -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-plus text-blue-600 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Permintaan Dibuat</p>
                                <p class="text-xs text-gray-500">{{ $paymentRequest->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        @if($paymentRequest->status !== 'pending')
                            <!-- Processed -->
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 {{ $paymentRequest->status === 'approved' ? 'bg-green-100' : 'bg-red-100' }} rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $paymentRequest->status === 'approved' ? 'fa-check' : 'fa-times' }} {{ $paymentRequest->status === 'approved' ? 'text-green-600' : 'text-red-600' }} text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $paymentRequest->status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $paymentRequest->approved_at ? $paymentRequest->approved_at->format('d M Y, H:i') : $paymentRequest->updated_at->format('d M Y, H:i') }}
                                    </p>
                                    @if($paymentRequest->approver)
                                        <p class="text-xs text-gray-600 mt-1">oleh {{ $paymentRequest->approver->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.projects.show', $paymentRequest->project) }}" 
                       class="block w-full bg-white text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium border border-gray-200">
                        <i class="fas fa-project-diagram mr-2"></i> Lihat Project
                    </a>
                    <a href="{{ route('admin.karyawan.show', $paymentRequest->user->id) }}" 
                       class="block w-full bg-white text-gray-700 px-4 py-3 rounded-lg hover:bg-gray-50 transition text-sm font-medium border border-gray-200">
                        <i class="fas fa-user mr-2"></i> Lihat Profil Employee
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
