# Fleetify Frontend (Laravel)

Employee attendance system frontend implementing UI for CRUD Karyawan, CRUD Departemen, Absen Masuk/Keluar, and attendance logs with filters and punctuality badges.

## Features

- Dashboard: quick clock in/out with employee search suggestions
- Attendance Log: filter by date and departement, punctuality badges
- Employees: list, search, filter, create, edit, delete (with cascade delete option)
- Departements: list, create, edit, delete with safety checks

## Tech Stack

- PHP 8.x
- Laravel 10.x
- Bootstrap 5 (CDN) and Bootstrap Icons (CDN)

## Repository Layout

- Routes: [routes/web.php](/fleetify-frontend/routes/web.php)
- Controllers:
  - [App\Http\Controllers\AttendanceController](fleetify-frontend/app/Http/Controllers/AttendanceController.php)
  - [App\Http\Controllers\EmployeeController](fleetify-frontend/app/Http/Controllers/EmployeeController.php)
  - [App\Http\Controllers\DepartementController](fleetify-frontend/app/Http/Controllers/DepartementController.php)
- Views:
  - Layout: [resources/views/layouts/app.blade.php](fleetify-frontend/resources/views/layouts/app.blade.php)
  - Dashboard: [resources/views/attendances/dashboard.blade.php](fleetify-frontend/resources/views/attendances/dashboard.blade.php)
  - Attendance Log: [resources/views/attendances/log.blade.php](fleetify-frontend/resources/views/attendances/log.blade.php)
  - Employees: [index](fleetify-frontend/resources/views/employees/index.blade.php:1), [create](fleetify-frontend/resources/views/employees/create.blade.php), [edit](fleetify-frontend/resources/views/employees/edit.blade.php)
  - Departements: [index](fleetify-frontend/resources/views/departements/index.blade.php), [create](fleetify-frontend/resources/views/departements/create.blade.php), [edit](fleetify-frontend/resources/views/departements/edit.blade.php)
- Backend base URL config: [config/backend.php](fleetify-frontend/config/backend.php)

## Requirements

- PHP 8.1+
- Composer

## Configuration

Set backend base URL via env and config:

- Env example: [/.env.example](fleetify-frontend/.env.example)
- Config reference: [config/backend.php](fleetify-frontend/config/backend.php)

Example .env:

```
BACKEND_BASE_URL=http://localhost:8080
```

The controllers read `config('backend.base_url')` to call backend APIs; avoid hardcoding URLs.

## Running Locally

1) Install dependencies:

```
composer install
```

2) Copy env and set backend URL:

```
cp .env.example .env
# edit .env to set BACKEND_BASE_URL if needed
```

3) Start dev server:

```
php artisan serve --host=127.0.0.1 --port=8000
```

Ensure the backend is running and accessible at BACKEND_BASE_URL.

## Routes and Pages

- `/` redirects to `/dashboard`.
- `/dashboard` Dashboard Absensi.
- `/attendance-log` Log Absensi, with filters.
- `/employees` list and search/filter employees.
- `/employees/create` create employee.
- `/employees/{id}/edit` edit employee.
- `/departements` list departements.
- `/departements/create` create departement.
- `/departements/{id}/edit` edit departement.

See [routes/web.php](fleetify-frontend/routes/web.php).

## API Integrations

Controllers call backend endpoints:

- Employees:
  - List: GET `${BASE}/api/employees?q=&departement_id=`
  - Create: POST `${BASE}/api/employees`
  - Detail: GET `${BASE}/api/employees/:id`
  - Update: PUT `${BASE}/api/employees/:id`
  - Delete: DELETE `${BASE}/api/employees/:id` (append `?cascade=1` and header `X-Cascade: 1` to cascade)

- Departements:
  - List: GET `${BASE}/api/departements`
  - Create: POST `${BASE}/api/departements`
  - Detail: GET `${BASE}/api/departements/:id`
  - Update: PUT `${BASE}/api/departements/:id`
  - Delete: DELETE `${BASE}/api/departements/:id`

- Attendance:
  - Clock In: POST `${BASE}/api/attendance/clock-in` with `{ employee_id }`
  - Clock Out: PUT `${BASE}/api/attendance/clock-out` with `{ employee_id }`
  - Logs: GET `${BASE}/api/attendances/log?date=&departement_id=`

## UI/UX Details

- Dashboard uses suggestions for employee selection; form requires choosing a valid employee id before submission.
- Attendance log displays badges:
  - Clock-in: Datang Lebih Cepat, Tepat Waktu, Terlambat
  - Clock-out: Pulang Cepat, Tepat Waktu, Pulang Lambat, Belum Clock Out
- Employees page provides:
  - Search by name/employee_id/departement
  - Filter by departement
  - Delete options:
    - Normal delete (fails with 409 if attendance exists)
    - Cascade delete (removes employee and all logs)

## Error Handling

- Backend error messages are surfaced to the UI via session alerts.
- Forms use Laravel validation; invalid-feedback is shown for required fields.

## Development Notes

- Do not commit `.env`; use [/.env.example](fleetify-frontend/.env.example)
- Git ignores runtime/cache and local DB files; see [.gitignore](fleetify-frontend/.gitignore)
- Assets are loaded from CDN (Bootstrap, Icons); Vite is not required for this challenge setup.

## Backend Database Setup (Required)

To ensure the frontend can display meaningful data from the backend, prepare the MySQL database using the provided SQL dump.

- SQL dump location: [fleetify_test.sql](../../fleetify_test.sql) (repository root)
- The dump includes:
  - Schema, indexes, and foreign keys for departement, employee, attendance, attendance_history
  - Seed/sample data for immediate testing

Import options:

- From repository root:
  ```
  mysql -u root -p -h 127.0.0.1 -P 3306 fleetify_test < fleetify_test.sql
  ```

- From frontend directory:
  ```
  mysql -u root -p -h 127.0.0.1 -P 3306 fleetify_test < ../fleetify_test.sql
  ```

- Via phpMyAdmin:
  - Open database "fleetify_test"
  - Import → select [fleetify_test.sql](../../fleetify_test.sql) → Start

Ensure that BACKEND_BASE_URL in your [.env](fleetify-frontend/.env.example) points to the running backend instance that connects to this database.

## Error Handling and 404

- Custom 404 page is provided and will render for unknown routes:
  - Fallback route: [routes/web.php](fleetify-frontend/routes/web.php)
  - View: [resources/views/errors/404.blade.php](fleetify-frontend/resources/views/errors/404.blade.php)
- Backend errors from API calls are surfaced as session alerts in the UI; forms use Laravel validation with invalid-feedback.
