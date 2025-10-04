@extends('layouts.app')

@section('title', 'Tambah Departemen — Fleetify')

@push('styles')
    <style>
        .form-actions .btn {
            min-width: 140px;
        }

        @media (max-width: 576px) {
            .form-actions .btn {
                min-width: auto;
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-2 mt-md-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="glass card">
                    <div
                        class="card-header bg-transparent border-0 pt-4 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <h1 class="page-title h4 fw-bold mb-0">
                            <i class="bi bi-diagram-3 me-2" aria-hidden="true"></i> Tambah Departemen
                        </h1>
                        <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                        </a>
                    </div>

                    <div class="card-content">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('departements.store') }}" method="POST" class="mt-2" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="departement_name" class="form-label fw-semibold">Nama Departemen</label>
                                <input type="text" id="departement_name" name="departement_name"
                                    class="form-control @error('departement_name') is-invalid @enderror"
                                    value="{{ old('departement_name') }}" placeholder="Contoh: Teknologi" required>
                                @error('departement_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label for="max_clock_in_time" class="form-label fw-semibold">Maks. Clock In</label>
                                    <input type="time" step="1" id="max_clock_in_time" name="max_clock_in_time"
                                        class="form-control @error('max_clock_in_time') is-invalid @enderror"
                                        value="{{ old('max_clock_in_time', '08:00:00') }}" required>
                                    @error('max_clock_in_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="max_clock_out_time" class="form-label fw-semibold">Maks. Clock Out</label>
                                    <input type="time" step="1" id="max_clock_out_time" name="max_clock_out_time"
                                        class="form-control @error('max_clock_out_time') is-invalid @enderror"
                                        value="{{ old('max_clock_out_time', '17:00:00') }}" required>
                                    @error('max_clock_out_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 form-actions mt-4">
                                <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1" aria-hidden="true"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-gradient-primary">
                                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan
                                </button>
                            </div>
                        </form>

                        <div class="small text-muted-2 mt-3">
                            Aturan status:
                            <ul class="mb-0">
                                <li><span class="text-success">Tepat Waktu</span>: Clock in ≤ maks. in, Clock out ≥ maks.
                                    out</li>
                                <li><span class="text-danger">Terlambat</span>: Clock in > maks. in</li>
                                <li><span class="text-warning">Pulang Cepat</span>: Clock out < maks. out</li>
                                <li><span class="text-muted">Belum Clock Out</span>: clock_out masih NULL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
