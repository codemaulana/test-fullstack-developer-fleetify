@extends('layouts.app')

@section('title', 'Log Absensi — Fleetify')

@push('styles')
    <style>
        .legend .badge {
            margin-right: .35rem;
            margin-bottom: .35rem;
        }

        .filters .btn {
            white-space: nowrap;
        }

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
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-12 col-lg-10">
                <div class="glass card">
                    <div class="card-header bg-transparent border-0 pt-4">
                        <h1 class="page-title fw-bold mb-1">
                            <i class="bi bi-clipboard-data me-2" aria-hidden="true"></i>
                            Log Absensi Karyawan
                        </h1>
                        <div class="text-muted-2 small">Laporan harian kehadiran dan ketepatan waktu</div>
                    </div>

                    <div class="card-content">
                        <form action="{{ route('attendance.log') }}" method="GET" class="mb-3 filters" role="search">
                            <div class="row g-3 align-items-end">
                                <div class="col-12 col-md-4">
                                    <label for="date" class="form-label fw-semibold">Tanggal</label>
                                    <input type="date" class="form-control" id="date" name="date"
                                        value="{{ request('date') }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="departement_id" class="form-label fw-semibold">Departemen</label>
                                    <select name="departement_id" id="departement_id" class="form-select"
                                        aria-label="Pilih departemen">
                                        <option value="">Semua Departemen</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept['id'] }}"
                                                {{ request('departement_id') == $dept['id'] ? 'selected' : '' }}>
                                                {{ $dept['departement_name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12 col-md-4 d-flex gap-2">
                                    <button type="submit" class="btn btn-gradient-primary flex-grow-1">
                                        <i class="bi bi-funnel me-1" aria-hidden="true"></i> Filter
                                    </button>
                                    <a href="{{ route('attendance.log') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="legend mb-3 small">
                            <span class="badge bg-success"><i class="bi bi-check2"></i> Tepat Waktu</span>
                            <span class="badge bg-info text-dark"><i class="bi bi-fast-forward"></i> Datang Lebih
                                Cepat</span>
                            <span class="badge bg-danger"><i class="bi bi-alarm"></i> Terlambat</span>
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i> Pulang Cepat</span>
                            <span class="badge bg-primary"><i class="bi bi-hourglass-bottom"></i> Pulang Lambat</span>
                            <span class="badge bg-secondary"><i class="bi bi-hourglass-split"></i> Belum Clock Out</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Nama Karyawan</th>
                                        <th>Departemen</th>
                                        <th>Jam Masuk</th>
                                        <th>Status Masuk</th>
                                        <th>Jam Keluar</th>
                                        <th>Status Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        @php
                                            $deptName = is_array($log['departement_name'])
                                                ? $log['departement_name']['String'] ?? '-'
                                                : $log['departement_name'] ?? '-';

                                            $co = $log['clock_out'] ?? null;
                                            $clockOutValid = is_array($co) ? $co['Valid'] ?? false : !empty($co);
                                            $clockOutRaw = is_array($co)
                                                ? $co['Time'] ?? ($co['String'] ?? '')
                                                : $co ?? '';

                                            $clockInFmt = \Carbon\Carbon::parse($log['clock_in'])->format(
                                                'Y-m-d H:i:s',
                                            );
                                            $clockOutFmt = $clockOutValid
                                                ? \Carbon\Carbon::parse($clockOutRaw)->format('Y-m-d H:i:s')
                                                : '-';
                                            $cos = $log['clock_out_status'] ?? 'Belum Clock Out';
                                        @endphp
                                        <tr>
                                            <td class="fw-semibold">{{ $log['employee_name'] }}</td>
                                            <td>{{ $deptName }}</td>
                                            <td>{{ $clockInFmt }}</td>
                                            <td>
                                                @php $cis = $log['clock_in_status'] ?? 'Tepat Waktu'; @endphp
                                                @if ($cis === 'Terlambat')
                                                    <span class="badge bg-danger">{{ $cis }}</span>
                                                @elseif ($cis === 'Datang Lebih Cepat')
                                                    <span class="badge bg-info text-dark">{{ $cis }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $cis }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $clockOutFmt }}</td>
                                            <td>
                                                @if ($cos === 'Pulang Cepat')
                                                    <span class="badge bg-warning text-dark">{{ $cos }}</span>
                                                @elseif ($cos === 'Pulang Lambat')
                                                    <span class="badge bg-primary">{{ $cos }}</span>
                                                @elseif ($cos === 'Tepat Waktu')
                                                    <span class="badge bg-success">{{ $cos }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $cos }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="bi bi-info-circle me-1"></i> Tidak ada data log absensi yang
                                                ditemukan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
