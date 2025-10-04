@extends('layouts.app')

@section('title', 'Dashboard Absensi — Fleetify')

@push('styles')
    <style>
        .hero-time {
            color: var(--muted);
        }

        .card-cta .btn {
            padding: .9rem 1.1rem;
        }

        .card-cta .btn .bi {
            font-size: 1.1rem;
        }

        .notice-area {
            min-height: 64px;
        }

        .alert.notice {
            max-width: 520px;
            margin: 0 auto;
        }

        @media (max-width: 576px) {
            .card-cta .btn {
                padding: .8rem 1rem;
                font-size: .95rem;
            }

            .alert.notice {
                max-width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <div class="glass card text-center">
                    <div class="card-header bg-transparent border-0 pt-4">
                        <h1 class="page-title fw-bold mb-1">
                            <i class="bi bi-calendar-check me-2" aria-hidden="true"></i>
                            Dashboard Absensi
                        </h1>
                        <div class="text-muted-2 small">Catat kehadiran Anda dengan cepat dan aman</div>
                        <div class="hero-time small mt-2" id="now" aria-live="polite"></div>
                    </div>

                    <div class="card-content">
                        <div class="notice-area">
                            @if (session('success'))
                                <div id="serverNotice" class="alert alert-success shadow-sm notice" role="alert"
                                    aria-label="berhasil">
                                    <i class="bi bi-check-circle me-2" aria-hidden="true"></i> {{ session('success') }}
                                </div>
                            @elseif (session('error'))
                                <div id="serverNotice" class="alert alert-danger shadow-sm notice" role="alert"
                                    aria-label="gagal">
                                    <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
                                    {{ session('error') }}
                                </div>
                            @else
                                <div id="serverNotice" class="alert notice invisible" aria-hidden="true"></div>
                            @endif

                        </div>

                        <form id="attendanceForm" action="{{ route('attendance.action') }}" method="POST"
                            class="text-start" novalidate>
                            @csrf
                            <div class="mb-3">
                                <label for="employee_id" class="form-label fw-semibold">Pilih Nama Anda</label>
                                <input type="text" name="employee_search" id="employee_search" class="form-control"
                                    autocomplete="off" placeholder="Ketik nama, departemen, atau ID…" required
                                    aria-required="true" aria-label="Cari dan pilih karyawan" />
                                <input type="hidden" name="employee_id" id="employee_id" />
                                <datalist id="employeeList">
                                    @foreach ($employees as $employee)
                                        @php
                                            $deptName = is_array($employee['departement_name'])
                                                ? $employee['departement_name']['String'] ?? ''
                                                : $employee['departement_name'] ?? '';
                                            $label =
                                                ($employee['name'] ?? '') . ' — ' . ($deptName ?: 'Tanpa Departemen');
                                        @endphp
                                        <option value="{{ $employee['employee_id'] }}" label="{{ $label }}"></option>
                                    @endforeach
                                </datalist>
                                <div id="employeeSuggest" class="list-group mt-2 d-none" role="listbox"
                                    aria-label="Saran karyawan"></div>
                                <small class="text-muted">Ketik nama, departemen, atau ID untuk menampilkan saran, gunakan
                                    panah atas/bawah dan Enter untuk memilih.</small>
                                <div id="clientError" class="invalid-feedback d-none" role="alert" aria-live="polite"
                                    aria-label="error"></div>
                            </div>
                            <div class="card-cta d-grid gap-3 mt-4">
                                <button type="submit" name="action" value="clock_in"
                                    class="btn btn-gradient-primary btn-lg" aria-label="Clock In">
                                    <i class="bi bi-door-open me-2" aria-hidden="true"></i> Clock In
                                </button>
                                <button type="submit" name="action" value="clock_out" class="btn btn-gradient-cyan btn-lg"
                                    aria-label="Clock Out">
                                    <i class="bi bi-door-closed me-2" aria-hidden="true"></i> Clock Out
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-4">
                        <div class="small text-muted-2">Pilih nama Anda lalu tekan tombol aksi.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const nowEl = document.getElementById('now');

            function updateNow() {
                const d = new Date();
                nowEl.textContent = d.toLocaleString();
            }
            updateNow();
            setInterval(updateNow, 1000);

            const employeeSearch = document.getElementById('employee_search');
            const hiddenEmployeeId = document.getElementById('employee_id');
            const datalist = document.getElementById('employeeList');
            const suggestBox = document.getElementById('employeeSuggest');
            let items = [];

            if (datalist) {
                items = Array.from(datalist.querySelectorAll('option')).map(o => {
                    const label = o.getAttribute('label') || '';
                    const parts = label.split(' — ');
                    const name = (parts[0] || '').trim();
                    const dept = (parts[1] || '').trim();
                    return {
                        id: o.value,
                        label,
                        name,
                        dept
                    };
                });
            }

            let filtered = [];
            let activeIndex = -1;

            function renderSuggestions(list) {
                suggestBox.innerHTML = '';
                if (!list.length) {
                    suggestBox.classList.add('d-none');
                    return;
                }
                suggestBox.classList.remove('d-none');
                list.slice(0, 8).forEach((item, idx) => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action';
                    a.textContent = item.label;
                    a.setAttribute('role', 'option');
                    if (idx === activeIndex) a.classList.add('active');
                    a.addEventListener('mousedown', (e) => {
                        e.preventDefault();
                        choose(item);
                    });
                    suggestBox.appendChild(a);
                });
            }

            function choose(item) {
                employeeSearch.value = item.label;
                hiddenEmployeeId.value = item.id;
                employeeSearch.classList.remove('is-invalid');
                renderSuggestions([]);
            }

            if (employeeSearch) {
                employeeSearch.addEventListener('input', (e) => {
                    const q = e.target.value.trim().toLowerCase();
                    activeIndex = -1;

                    filtered = items.filter(it =>
                        it.name.toLowerCase().includes(q) ||
                        it.dept.toLowerCase().includes(q) ||
                        it.id.toLowerCase().includes(q)
                    );

                    if (hiddenEmployeeId) hiddenEmployeeId.value = '';

                    if (!filtered.length) {
                        renderSuggestions([]);
                    } else {
                        renderSuggestions(filtered);
                    }
                });

                employeeSearch.addEventListener('keydown', (e) => {
                    const keys = ['ArrowDown', 'ArrowUp', 'Enter', 'Escape'];
                    if (keys.includes(e.key) && !suggestBox.classList.contains('d-none')) {
                        e.preventDefault();
                    }
                    if (e.key === 'ArrowDown') {
                        activeIndex = Math.min(activeIndex + 1, filtered.length - 1);
                        renderSuggestions(filtered);
                    } else if (e.key === 'ArrowUp') {
                        activeIndex = Math.max(activeIndex - 1, 0);
                        renderSuggestions(filtered);
                    } else if (e.key === 'Enter') {
                        if (activeIndex >= 0 && filtered[activeIndex]) {
                            e.preventDefault();
                            choose(filtered[activeIndex]);
                        }
                    } else if (e.key === 'Escape') {
                        renderSuggestions([]);
                    }
                });

                employeeSearch.addEventListener('blur', () => setTimeout(() => renderSuggestions([]), 150));
            }
            const form = document.getElementById('attendanceForm');
            const clientErr = document.getElementById('clientError');
            const serverNotice = document.getElementById('serverNotice');

            function hasServerMessage() {
                return !!(serverNotice && !serverNotice.classList.contains('invisible') && !serverNotice.classList
                    .contains('d-none') && serverNotice.textContent.trim().length > 0);
            }

            function showClientError(msg) {
                if (!clientErr) return;
                if (hasServerMessage()) return;
                clientErr.textContent = msg;
                clientErr.classList.remove('d-none');
            }

            const allowedIDs = items.map(it => it.id);

            if (form) {
                form.addEventListener('submit', (e) => {
                    const val = hiddenEmployeeId ? hiddenEmployeeId.value.trim() : '';
                    if (!val || !allowedIDs.includes(val)) {
                        e.preventDefault();
                        if (employeeSearch) {
                            employeeSearch.classList.add('is-invalid');
                            employeeSearch.focus();
                        }
                        showClientError('Silakan pilih nama Anda dari daftar saran.');
                        return;
                    }

                    if (employeeSearch) employeeSearch.classList.remove('is-invalid');
                    if (clientErr) clientErr.classList.add('d-none');
                });
            }
        })();
    </script>
@endpush
