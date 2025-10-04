<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title>@yield('title', 'Fleetify Absensi')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    @stack('head')
    <style>
        :root {
            --bg-gradient: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
            --surface: #ffffff;
            --surface-border: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --brand: #4f46e5;
            --brand-2: #6366f1;
            --accent: #14b8a6;
            --accent-2: #0d9488;
            --radius: 16px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg-gradient);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .glass {
            background: var(--surface);
            backdrop-filter: none;
            border: 1px solid var(--surface-border);
            border-radius: var(--radius);
            box-shadow: 0 8px 24px rgba(2, 6, 23, .08);
        }

        .page-container {
            padding: 24px 16px;
        }

        @media (min-width: 768px) {
            .page-container {
                padding: 32px 24px;
            }
        }

        .page-title {
            letter-spacing: .3px;
        }

        .text-muted-2 {
            color: var(--muted) !important;
        }

        .table thead {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8fafc;
        }

        .btn-gradient-primary {
            background: linear-gradient(135deg, var(--brand), var(--brand-2));
            border: none;
            color: #fff;
        }

        .btn-gradient-cyan {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            border: none;
            color: #fff;
        }

        .btn-secondary {
            background: #f8fafc;
            border: 1px solid var(--surface-border);
            color: #0f172a;
        }

        .btn:focus {
            outline: 2px solid #818cf8;
            outline-offset: 2px;
        }

        .form-control,
        .form-select {
            background: #ffffff;
            border-color: #d0d7de;
            color: #0f172a;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 .25rem rgba(99, 102, 241, .25);
            border-color: #818cf8;
        }

        .nav-elevated {
            background: #ffffff !important;
            backdrop-filter: none;
            border-bottom: 1px solid var(--surface-border);
            box-shadow: 0 6px 20px rgba(2, 6, 23, .06);
        }

        .navbar .nav-link {
            color: #334155;
        }

        .navbar .nav-link:hover {
            color: #0f172a;
        }

        .navbar .nav-link.active {
            color: #0f172a;
            font-weight: 600;
        }

        .navbar .nav-link.active::after {
            content: '';
            display: block;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), var(--accent));
            border-radius: 2px;
            margin-top: 6px;
        }

        footer .small {
            color: var(--muted);
        }

        .chip {
            padding: .25rem .55rem;
            border-radius: 999px;
            background: #f1f5f9;
            color: #0f172a;
            font-size: .8rem;
        }

        .card-content {
            padding: 1rem;
        }

        @media (min-width: 576px) {
            .card-content {
                padding: 1.25rem;
            }
        }

        @media (min-width: 992px) {
            .card-content {
                padding: 1.5rem;
            }
        }

        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, .45);
            border-radius: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light nav-elevated">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('attendance.dashboard') }}">
                <i class="bi bi-clipboard-check me-2"></i> Fleetify Absensi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('attendance.dashboard') ? 'active fw-semibold' : '' }}"
                            href="{{ route('attendance.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>
                            Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('attendance.log') ? 'active fw-semibold' : '' }}"
                            href="{{ route('attendance.log') }}"><i class="bi bi-clipboard-data me-1"></i> Log
                            Absensi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('employees.*') ? 'active fw-semibold' : '' }}"
                            href="{{ route('employees.index') }}"><i class="bi bi-people me-1"></i> Karyawan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departements.*') ? 'active fw-semibold' : '' }}"
                            href="{{ route('departements.index') }}"><i class="bi bi-diagram-3 me-1"></i> Departemen</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="page-container">
        @yield('content')
    </main>
    <footer class="container pb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="small">© {{ date('Y') }} Fleetify Absensi</div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
