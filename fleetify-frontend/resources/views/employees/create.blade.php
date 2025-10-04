@extends('layouts.app')

@section('title', 'Tambah Karyawan — Fleetify')

@push('styles')
    <style>
        .form-wide-label {
            color: var(--text);
        }

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
                            <i class="bi bi-person-plus me-2" aria-hidden="true"></i> Tambah Karyawan Baru
                        </h1>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                        </a>
                    </div>
                    <div class="card-content">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('employees.store') }}" method="POST" class="mt-2" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="employee_id" class="form-label fw-semibold">ID Karyawan (unik)</label>
                                <input type="text" id="employee_id" name="employee_id"
                                    class="form-control @error('employee_id') is-invalid @enderror"
                                    value="{{ old('employee_id') }}" placeholder="Contoh: KRYWN01" required>
                                @error('employee_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="Nama lengkap karyawan" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold">Alamat</label>
                                <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                    placeholder="Alamat lengkap" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="departement_id" class="form-label fw-semibold">Departemen</label>
                                <select id="departement_id" name="departement_id"
                                    class="form-select @error('departement_id') is-invalid @enderror" required
                                    aria-label="Pilih departemen">
                                    <option value="">Pilih Departemen</option>
                                    @forelse ($departments as $dept)
                                        @php
                                            $id = $dept['id'] ?? ($dept['ID'] ?? null);
                                            $name =
                                                $dept['departement_name'] ?? ($dept['DepartementName'] ?? 'Tanpa nama');
                                        @endphp
                                        <option value="{{ $id }}"
                                            {{ old('departement_id') == $id ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @empty
                                        <option value="" disabled>Gagal memuat departemen</option>
                                    @endforelse
                                </select>
                                @error('departement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex flex-wrap gap-2 form-actions mt-4">
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1" aria-hidden="true"></i> Batal
                                </a>
                                <button type="submit" class="btn btn-gradient-primary">
                                    <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Simpan
                                </button>
                            </div>
                        </form>

                        <div class="small text-muted-2 mt-3">
                            Catatan: Pastikan ID Karyawan unik. Penentuan status log absensi mengacu pada jam maksimal
                            departemen karyawan.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
