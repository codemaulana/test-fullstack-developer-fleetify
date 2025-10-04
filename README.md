# Fleetify Absensi — Fullstack Challenge

Single documentation covering both backend (Golang + Gin + MySQL) and frontend (Laravel). Located at repository root, alongside fleetify-backend and fleetify-frontend.

Overview
- Implements CRUD Karyawan, CRUD Departemen, Clock In/Out, Attendance Logs with punctuality and filters.
- Two projects: Backend in Golang, Frontend in Laravel.
- Configurable base URL for frontend; environment examples provided; gitignore hardened.

Repository Structure
- Backend: [fleetify-backend/](fleetify-backend/README.md:1)
- Frontend: [fleetify-frontend/](fleetify-frontend/README.md:1)
- Root doc: this file.

Prerequisites
- Windows 11 or compatible OS
- Go 1.22+
- MySQL 5.7+/8.x
- PHP 8.1+, Composer

Quick Start
Backend:
- Configure env via [backend .env.example](fleetify-backend/.env.example:1).
- Prepare database:
  - Ensure MySQL is running and database "fleetify_test" exists.
  - Import [fleetify_test.sql](fleetify_test.sql:1) (schema + constraints + sample data).
    - CMD: mysql -u root -p -h 127.0.0.1 -P 3306 fleetify_test < fleetify_test.sql
    - Or via phpMyAdmin: Navigate to fleetify_test → Import → Select [fleetify_test.sql](fleetify_test.sql:1) → Start.
- Option A: set MYSQL_DSN directly.
- Option B: set DB_USER, DB_PASS, DB_HOST, DB_PORT, DB_NAME.
- Run:
  - CMD: set "PORT=8080"
  - CMD: set "MYSQL_DSN=root:@tcp(127.0.0.1:3306)/fleetify_test?parseTime=true&loc=Local"
  - CMD: go run [main.go](fleetify-backend/main.go:25)

Frontend:
- Install deps: composer install
- Copy env: cp .env.example .env
- Set BACKEND_BASE_URL in [.env](fleetify-frontend/.env.example:1)
- Serve: php artisan serve --host=127.0.0.1 --port=8000
- Pages:
  - Dashboard: /dashboard
  - Attendance Log: /attendance-log
  - Employees: /employees (+ create/edit)
  - Departements: /departements (+ create/edit)

Configuration
- Frontend base URL: [config('backend.base_url')](fleetify-frontend/config/backend.php:1) consumed in controllers:
  - [EmployeeController.php](fleetify-frontend/app/Http/Controllers/EmployeeController.php:13)
  - [DepartementController.php](fleetify-frontend/app/Http/Controllers/DepartementController.php:13)
  - [AttendanceController.php](fleetify-frontend/app/Http/Controllers/AttendanceController.php:16)
- Backend env: read in [mustEnv()](fleetify-backend/main.go:17) and DSN build in [main.go](fleetify-backend/main.go:30).

Architecture
- Routes setup: [httpapi.SetupRoutes()](fleetify-backend/internal/interfaces/http/router.go:5).
- Handlers map HTTP to services:
  - [httpapi.EmployeeHandler](fleetify-backend/internal/interfaces/http/employee_handler.go:1)
  - [httpapi.DepartementHandler](fleetify-backend/internal/interfaces/http/departement_handler.go:1)
  - [httpapi.AttendanceHandler](fleetify-backend/internal/interfaces/http/attendance_handler.go:1)
- Services orchestrate business rules:
  - [service.EmployeeService](fleetify-backend/internal/app/service/employee_service.go:1)
  - [service.DepartementService](fleetify-backend/internal/app/service/departement_service.go:1)
  - [service.AttendanceService](fleetify-backend/internal/app/service/attendance_service.go:1)
- Repositories encapsulate MySQL queries:
  - [mysql.EmployeeMySQLRepository](fleetify-backend/internal/infrastructure/repository/mysql/employee_repo.go:1)
  - [mysql.DepartementMySQLRepository](fleetify-backend/internal/infrastructure/repository/mysql/departement_repo.go:1)
  - [mysql.AttendanceMySQLRepository](fleetify-backend/internal/infrastructure/repository/mysql/attendance_repo.go:1)
- Frontend routes: [routes/web.php](fleetify-frontend/routes/web.php:1); Views under [resources/views](fleetify-frontend/resources/views/layouts/app.blade.php:1).

Database Schema (expected)
- departement(id, departement_name, max_clock_in_time, max_clock_out_time)
- employee(id, employee_id, name, address, departement_id)
- attendance(id, attendance_id, employee_id, clock_in, clock_out)
- attendance_history(id, attendance_id, employee_id, date_attendance, attendance_type, description)
Relations:
- employee.departement_id → departement.id
- attendance.employee_id → employee.employee_id

Endpoint Reference (Backend)
Base: http://localhost:8080/api

Departements
- GET /departements → list
- POST /departements → create
- GET /departements/:id → detail
- PUT /departements/:id → update
- DELETE /departements/:id → delete (409 if employees exist)

Employees
- GET /employees?q=&departement_id= → list with filters
- POST /employees → create
- GET /employees/:id → detail
- PUT /employees/:id → update
- DELETE /employees/:id → delete; cascade via ?cascade=1 and header X-Cascade: 1

Attendance
- POST /attendance/clock-in {employee_id}
- PUT /attendance/clock-out {employee_id}
- GET /attendances/log?date=YYYY-MM-DD&departement_id=ID

Punctuality Logic
- Implemented in [mysql.AttendanceMySQLRepository.QueryAttendanceLogs()](fleetify-backend/internal/infrastructure/repository/mysql/attendance_repo.go:81).
- Clock-in:
  - ≤ max_clock_in_time − 1h → "Datang Lebih Cepat"
  - ≤ max_clock_in_time → "Tepat Waktu"
  - else → "Terlambat"
- Clock-out:
  - NULL → "Belum Clock Out"
  - within ±30m of max_clock_out_time → "Tepat Waktu"
  - < max_clock_out_time − 30m → "Pulang Cepat"
  - > max_clock_out_time + 30m → "Pulang Lambat"

Cascade Delete Behavior
- Frontend signals cascade via query + header in [EmployeeController.destroy()](fleetify-frontend/app/Http/Controllers/EmployeeController.php:136).
- Backend detects truthy cascade in [httpapi.EmployeeHandler.Delete()](fleetify-backend/internal/interfaces/http/employee_handler.go:148) and performs transactional delete in [mysql.EmployeeMySQLRepository.DeleteCascadeByID()](fleetify-backend/internal/infrastructure/repository/mysql/employee_repo.go:138).

Frontend Pages
- Layout: [app.blade.php](fleetify-frontend/resources/views/layouts/app.blade.php:1)
- Employees Index: [index.blade.php](fleetify-frontend/resources/views/employees/index.blade.php:1)
- Employees Create: [create.blade.php](fleetify-frontend/resources/views/employees/create.blade.php:1)
- Employees Edit: [edit.blade.php](fleetify-frontend/resources/views/employees/edit.blade.php:1)
- Departements Index: [index.blade.php](fleetify-frontend/resources/views/departements/index.blade.php:1)
- Attendance Log: [log.blade.php](fleetify-frontend/resources/views/attendances/log.blade.php:1)
- Dashboard: [dashboard.blade.php](fleetify-frontend/resources/views/attendances/dashboard.blade.php:1)

Error Semantics
- 400 Bad Request on validation errors
- 404 Not Found for missing resources or no active attendance to clock-out
- 409 Conflict for business constraints (duplicate attendance, delete constraints)
- 500 Internal Server Error on unexpected failures
See handlers:
- [AttendanceHandler.ClockIn()](fleetify-backend/internal/interfaces/http/attendance_handler.go:25)
- [AttendanceHandler.ClockOut()](fleetify-backend/internal/interfaces/http/attendance_handler.go:51)
- [EmployeeHandler.Delete()](fleetify-backend/internal/interfaces/http/employee_handler.go:148)
- [DepartementHandler.Delete()](fleetify-backend/internal/interfaces/http/departement_handler.go:130)

Sample Requests
Create departement:
POST /api/departements
Content-Type: application/json
{ "departement_name": "IT", "max_clock_in_time": "09:00:00", "max_clock_out_time": "18:00:00" }

Clock in:
POST /api/attendance/clock-in
Content-Type: application/json
{ "employee_id": "EMP001" }

Clock out:
PUT /api/attendance/clock-out
Content-Type: application/json
{ "employee_id": "EMP001" }

Delete employee (cascade):
DELETE /api/employees/123?cascade=1
X-Cascade: 1

Git Hygiene
- Do not commit .env; use examples:
  - [frontend/.env.example](fleetify-frontend/.env.example:1)
  - [backend/.env.example](fleetify-backend/.env.example:1)
- Ignore artefacts: see [.gitignore frontend](fleetify-frontend/.gitignore:1) and [.gitignore backend](fleetify-backend/.gitignore:1).

Troubleshooting
- Port conflict: set [PORT](fleetify-backend/.env.example:1) to a free port, update FRONTEND BACKEND_BASE_URL.
- MySQL auth: verify user and privileges; ensure database exists.
- Timezone: app uses Asia/Jakarta for attendance; ensure server clock is correct.

Notes
- A GORM layer exists but is not wired: [database/mysql.go](fleetify-backend/internal/infrastructure/database/mysql.go:14), [models](fleetify-backend/models/departement.go:5).
- Current implementation uses database/sql.