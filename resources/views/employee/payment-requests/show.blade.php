@extends('layouts.app')

@section('page-title', 'Detail Payment Request')

@section('content')
<div class="p-8">
    <div class="mb-6">
        <a href="{{ route('employee.payment-requests.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="text-2xl font-bold text-gray-900 mb-6">Detail Permintaan Pembayaran</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex border-b border-gray-100 pb-3">
                            <div class="w-1/3 font-semibold text-gray-700">Status:</div>
                            <div class="w-2/3">
                                @if($paymentRequest->status === 'pending')
                                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Pending</span>
                                @elseif($paymentRequest->status === 'approved')
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Disetujui</span>
                                @elseif($paymentRequest->status === 'processing')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">Diproses</span>
                                @elseif($paymentRequest->status === 'paid')
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-xs font-semibold rounded-full">Dibayar</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Ditolak</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex border-b border-gray-100 pb-3">
                            <div class="w-1/3 font-semibold text-gray-700">Tanggal Pengajuan:</div>
                            <div class="w-2/3 text-gray-900">{{ $paymentRequest->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="flex border-b border-gray-100 pb-3">
                            <div class="w-1/3 font-semibold text-gray-700">Project / Kelas:</div>
                            <div class="w-2/3 text-gray-900">
                                @if($paymentRequest->project)
                                    {{ $paymentRequest->project->project_name }}
                                @elseif($paymentRequest->clas)
                                    {{ $paymentRequest->clas->name }} (Academy)
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        <div class="flex border-b border-gray-100 pb-3">
                            <div class="w-1/3 font-semibold text-gray-700">Nominal Diajukan:</div>
                            <div class="w-2/3 text-blue-600 font-bold text-lg">
                                Rp {{ number_format($paymentRequest->requested_amount, 0, ',', '.') }}
                            </div>
                        </div>

                        @if($paymentRequest->approved_amount)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Nominal Disetujui:</div>
                                <div class="w-2/3 text-green-600 font-bold text-lg">
                                    Rp {{ number_format($paymentRequest->approved_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        @endif

                        @if($paymentRequest->hours_worked)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Jam Kerja:</div>
                                <div class="w-2/3 text-gray-900">{{ $paymentRequest->hours_worked }} jam</div>
                            </div>
                        @endif

                        @if($paymentRequest->notes)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Catatan Anda:</div>
                                <div class="w-2/3 text-gray-900">{{ $paymentRequest->notes }}</div>
                            </div>
                        @endif

                        @if($paymentRequest->admin_notes)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Catatan Admin:</div>
                                <div class="w-2/3 text-gray-900">{{ $paymentRequest->admin_notes }}</div>
                            </div>
                        @endif

                        @if($paymentRequest->approved_at)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Diproses Pada:</div>
                                <div class="w-2/3 text-gray-900">
                                    {{ $paymentRequest->approved_at->format('d/m/Y H:i') }}
                                    @if($paymentRequest->approver)
                                        oleh <span class="font-semibold">{{ $paymentRequest->approver->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($paymentRequest->paid_at)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Dibayar Pada:</div>
                                <div class="w-2/3 text-gray-900">
                                    {{ $paymentRequest->paid_at->format('d/m/Y H:i') }}
                                    @if($paymentRequest->payer)
                                        oleh <span class="font-semibold">{{ $paymentRequest->payer->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($paymentRequest->payment_method)
                            <div class="flex border-b border-gray-100 pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Metode Pembayaran:</div>
                                <div class="w-2/3 text-gray-900">{{ $paymentRequest->payment_method }}</div>
                            </div>
                        @endif

                        @if($paymentRequest->payment_reference)
                            <div class="flex pb-3">
                                <div class="w-1/3 font-semibold text-gray-700">Referensi Pembayaran:</div>
                                <div class="w-2/3 text-gray-900">{{ $paymentRequest->payment_reference }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-6 border border-indigo-200">
                <h3 class="text-lg font-semibold text-indigo-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i>
                    Informasi
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-indigo-700 font-medium">Status Pembayaran</p>
                        <p class="text-indigo-900 mt-1">
                            @if($paymentRequest->status === 'pending')
                                Menunggu review dari admin
                            @elseif($paymentRequest->status === 'approved')
                                Sudah disetujui, menunggu pembayaran
                            @elseif($paymentRequest->status === 'processing')
                                Sedang diproses untuk pembayaran
                            @elseif($paymentRequest->status === 'paid')
                                Pembayaran sudah selesai
                            @else
                                Pengajuan ditolak oleh admin
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
