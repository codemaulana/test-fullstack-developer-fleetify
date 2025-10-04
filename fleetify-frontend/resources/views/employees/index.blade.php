@extends('layouts.app')

@section('title', 'Karyawan — Fleetify')

@push('styles')
    <style>
        .table {
            font-size: .95rem;
        }

        @media (max-width: 576px) {
            .table {
                font-size: .9rem;
            }

            th,
            td {
                white-space: nowrap;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container mt-2 mt-md-3">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="glass card">
                    <div
                        class="card-header bg-transparent border-0 pt-4 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div>
                            <h1 class="page-title h3 fw-bold mb-1">
                                <i class="bi bi-people me-2" aria-hidden="true"></i> Manajemen Karyawan
                            </h1>
                            <div class="small text-muted-2">Kelola data karyawan dan penempatannya</div>
                        </div>
                        <a href="{{ route('employees.create') }}" class="btn btn-gradient-primary">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Tambah Karyawan Baru
                        </a>
                    </div>

                    <div class="card-content">
                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2" aria-hidden="true"></i>{{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>{{ session('error') }}
                            </div>
                        @endif

                        <form method="GET" action="{{ route('employees.index') }}"
                            class="row g-2 align-items-end mb-3 px-2">
                            <div class="col-md-5">
                                <label for="q" class="form-label fw-semibold">Cari</label>
                                <input type="text" id="q" name="q" class="form-control"
                                    placeholder="Nama, ID karyawan, atau Departemen" value="{{ $q ?? '' }}" />
                            </div>
                            <div class="col-md-4">
                                <label for="departement_id" class="form-label fw-semibold">Departemen</label>
                                <select id="departement_id" name="departement_id" class="form-select">
                                    <option value="">Semua Departemen</option>
                                    @foreach ($departments ?? [] as $dept)
                                        @php
                                            $deptId = $dept['id'] ?? ($dept['ID'] ?? '');
                                            $deptName =
                                                $dept['departement_name'] ??
                                                ($dept['DepartementName'] ?? 'Tanpa Departemen');
                                        @endphp
                                        <option value="{{ $deptId }}"
                                            {{ isset($departementId) && (string) $departementId === (string) $deptId ? 'selected' : '' }}>
                                            {{ $deptName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-search me-1" aria-hidden="true"></i> Cari
                                </button>
                                <a href="{{ route('employees.index') }}" class="btn btn-light">
                                    <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> Reset
                                </a>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>ID Karyawan</th>
                                        <th>Nama</th>
                                        <th>Alamat</th>
                                        <th>Departemen</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($employees as $employee)
                                        @php
                                            $deptName = is_array($employee['departement_name'] ?? null)
                                                ? $employee['departement_name']['String'] ?? 'Tidak ada'
                                                : $employee['departement_name'] ?? 'Tidak ada';
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $employee['employee_id'] }}</td>
                                            <td>{{ $employee['name'] }}</td>
                                            <td>{{ $employee['address'] }}</td>
                                            <td>{{ $deptName }}</td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots" aria-hidden="true"></i> Aksi
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('employees.edit', ['id' => $employee['id']]) }}">
                                                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>
                                                                Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('employees.destroy', ['id' => $employee['id']]) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash me-2" aria-hidden="true"></i>
                                                                    Hapus
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('employees.destroy', ['id' => $employee['id']]) }}?cascade=1"
                                                                method="POST"
                                                                onsubmit="return confirm('Hapus karyawan beserta seluruh log absensinya? Tindakan ini tidak dapat dikembalikan.');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="cascade" value="1">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bi bi-trash me-2" aria-hidden="true"></i>
                                                                    Hapus + Log
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <i class="bi bi-info-circle me-1" aria-hidden="true"></i> Tidak ada data
                                                karyawan atau gagal mengambil data dari API.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="small text-muted-2">
                            Tip: Pastikan setiap karyawan memiliki departemen, karena aturan ketepatan absensi mengacu pada
                            jam maksimal departemen.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
