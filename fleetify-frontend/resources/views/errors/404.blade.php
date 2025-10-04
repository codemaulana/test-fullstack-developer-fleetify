@extends('layouts.app')

@section('title', '404 — Halaman Tidak Ditemukan')

@push('styles')
    <style>
        .error-hero {
            text-align: center;
            padding: 40px 16px;
        }
        .error-code {
            font-size: 72px;
            font-weight: 800;
            letter-spacing: 2px;
        }
        .error-desc {
            color: var(--muted);
        }
        .error-actions .btn {
            min-width: 160px;
        }
    </style>
@endpush

@section('content')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="glass card">
                    <div class="card-content">
                        <div class="error-hero">
                            <div class="error-code">404</div>
                            <h1 class="h4 fw-bold mt-2">Halaman Tidak Ditemukan</h1>
                            <p class="error-desc mt-2">
                                URL yang Anda akses tidak tersedia atau sudah dipindahkan.
                            </p>
                            <div class="d-flex flex-wrap gap-2 justify-content-center error-actions mt-4">
                                <a href="{{ route('attendance.dashboard') }}" class="btn btn-gradient-primary">
                                    <i class="bi bi-speedometer2 me-1" aria-hidden="true"></i> Ke Dashboard
                                </a>
                                <a href="{{ route('attendance.log') }}" class="btn btn-secondary">
                                    <i class="bi bi-clipboard-data me-1" aria-hidden="true"></i> Lihat Log Absensi
                                </a>
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-people me-1" aria-hidden="true"></i> Kelola Karyawan
                                </a>
                                <a href="{{ route('departements.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-diagram-3 me-1" aria-hidden="true"></i> Kelola Departemen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection