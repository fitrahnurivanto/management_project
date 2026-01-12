@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('employee.payment-requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <h2 class="mb-4">Ajukan Permintaan Pembayaran</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('employee.payment-requests.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select" required id="project-select">
                        <option value="">Pilih Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih project untuk payment request</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nominal yang Diajukan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="requested_amount" class="form-control" 
                               min="0" value="{{ old('requested_amount') }}" 
                               required id="amount-input">
                    </div>
                    <small class="text-muted">Masukkan nominal pembayaran yang ingin diajukan</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes') }}</textarea>
                    <small class="text-muted">Opsional - Jelaskan pekerjaan yang sudah diselesaikan</small>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Ajukan Permintaan
                    </button>
                    <a href="{{ route('employee.payment-requests.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
