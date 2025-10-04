@extends('layouts.app')

@section('title', 'Edit Departemen — Fleetify')

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
                            <i class="bi bi-diagram-3 me-2" aria-hidden="true"></i> Edit Departemen
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

                        @php
                            $id = $departement['id'] ?? ($departement['ID'] ?? null);
                            $name = $departement['departement_name'] ?? ($departement['DepartementName'] ?? '');
                            $in = $departement['max_clock_in_time'] ?? ($departement['MaxClockInTime'] ?? '08:00:00');
                            $out =
                                $departement['max_clock_out_time'] ?? ($departement['MaxClockOutTime'] ?? '17:00:00');
                        @endphp

                        @if (empty($id))
                            <div class="alert alert-danger">
                                Data departemen tidak ditemukan.
                            </div>
                        @else
                            <form action="{{ route('departements.update', ['id' => $id]) }}" method="POST" class="mt-2"
                                novalidate>
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="departement_name" class="form-label fw-semibold">Nama Departemen</label>
                                    <input type="text" id="departement_name" name="departement_name"
                                        class="form-control @error('departement_name') is-invalid @enderror"
                                        value="{{ old('departement_name', $name) }}" required>
                                    @error('departement_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <label for="max_clock_in_time" class="form-label fw-semibold">Maks. Clock In</label>
                                        <input type="time" step="1" id="max_clock_in_time" name="max_clock_in_time"
                                            class="form-control @error('max_clock_in_time') is-invalid @enderror"
                                            value="{{ old('max_clock_in_time', $in) }}" required>
                                        @error('max_clock_in_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="max_clock_out_time" class="form-label fw-semibold">Maks. Clock
                                            Out</label>
                                        <input type="time" step="1" id="max_clock_out_time"
                                            name="max_clock_out_time"
                                            class="form-control @error('max_clock_out_time') is-invalid @enderror"
                                            value="{{ old('max_clock_out_time', $out) }}" required>
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
                                        <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        @endif

                        <div class="small text-muted-2 mt-3">
                            Aturan status di Log Absensi:
                            <ul class="mb-0">
                                <li>Tepat Waktu: Clock in ≤ maks. in, Clock out ≥ maks. out</li>
                                <li>Terlambat: Clock in > maks. in</li>
                                <li>Pulang Cepat: Clock out < maks. out</li>
                                <li>Belum Clock Out: clock_out NULL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
