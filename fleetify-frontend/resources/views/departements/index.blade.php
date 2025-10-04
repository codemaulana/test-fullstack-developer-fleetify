@extends('layouts.app')

@section('title', 'Departemen — Fleetify')

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
                                <i class="bi bi-diagram-3 me-2" aria-hidden="true"></i> Manajemen Departemen
                            </h1>
                            <div class="small text-muted-2">Atur departemen serta jam maksimal masuk dan keluar</div>
                        </div>
                        <a href="{{ route('departements.create') }}" class="btn btn-gradient-primary">
                            <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Tambah Departemen
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

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Departemen</th>
                                        <th>Maks. Jam Masuk</th>
                                        <th>Maks. Jam Keluar</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($departements as $dept)
                                        @php
                                            $id = $dept['id'] ?? ($dept['ID'] ?? null);
                                            $name = $dept['departement_name'] ?? ($dept['DepartementName'] ?? '-');
                                            $in = $dept['max_clock_in_time'] ?? ($dept['MaxClockInTime'] ?? '-');
                                            $out = $dept['max_clock_out_time'] ?? ($dept['MaxClockOutTime'] ?? '-');
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $name }}</td>
                                            <td>{{ $in }}</td>
                                            <td>{{ $out }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('departements.edit', ['id' => $id]) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="bi bi-pencil-square" aria-hidden="true"></i> Edit
                                                </a>
                                                <form action="{{ route('departements.destroy', ['id' => $id]) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus departemen ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash" aria-hidden="true"></i> Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <i class="bi bi-info-circle me-1" aria-hidden="true"></i> Tidak ada data
                                                departemen atau gagal mengambil data.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="small text-muted-2">
                            Catatan: Jam ketepatan absensi karyawan mengacu pada kebijakan departemen.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
