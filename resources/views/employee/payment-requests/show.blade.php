@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('employee.payment-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-4">Detail Permintaan Pembayaran</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-4 fw-bold">Status:</div>
                        <div class="col-8">
                            @if($paymentRequest->status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @elseif($paymentRequest->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @else
                                <span class="badge bg-danger">Ditolak</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-bold">Tanggal Pengajuan:</div>
                        <div class="col-8">{{ $paymentRequest->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-bold">Project:</div>
                        <div class="col-8">{{ $paymentRequest->project->project_name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-4 fw-bold">Nominal Diajukan:</div>
                        <div class="col-8 text-primary fw-bold">
                            Rp {{ number_format($paymentRequest->requested_amount, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($paymentRequest->approved_amount)
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Nominal Disetujui:</div>
                            <div class="col-8 text-success fw-bold">
                                Rp {{ number_format($paymentRequest->approved_amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @endif

                    @if($paymentRequest->notes)
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Catatan Anda:</div>
                            <div class="col-8">{{ $paymentRequest->notes }}</div>
                        </div>
                    @endif

                    @if($paymentRequest->admin_notes)
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Catatan Admin:</div>
                            <div class="col-8">{{ $paymentRequest->admin_notes }}</div>
                        </div>
                    @endif

                    @if($paymentRequest->approved_at)
                        <div class="row mb-3">
                            <div class="col-4 fw-bold">Diproses Pada:</div>
                            <div class="col-8">
                                {{ $paymentRequest->approved_at->format('d/m/Y H:i') }}
                                @if($paymentRequest->approver)
                                    oleh {{ $paymentRequest->approver->name }}
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
