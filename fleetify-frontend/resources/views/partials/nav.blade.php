<nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(255,255,255,0.25);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('attendance.dashboard') }}">
            <i class="bi bi-clipboard-check me-2"></i> Fleetify Absensi
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendance.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('attendance.dashboard') }}">
                        <i class="bi bi-speedometer2 me-1"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('attendance.log') ? 'active fw-semibold' : '' }}" href="{{ route('attendance.log') }}">
                        <i class="bi bi-clipboard-data me-1"></i> Log Absensi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('employees.*') ? 'active fw-semibold' : '' }}" href="{{ route('employees.index') }}">
                        <i class="bi bi-people me-1"></i> Karyawan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('departements.*') ? 'active fw-semibold' : '' }}" href="{{ route('departements.index') }}">
                        <i class="bi bi-diagram-3 me-1"></i> Departemen
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>