# Fleetify Backend (Golang + Gin + MySQL)

Employee attendance system backend implementing CRUD for employees and departements, clock-in/out operations, and attendance logs with punctuality calculation and filters.

## Features

- CRUD Karyawan (Employees)
- CRUD Departemen (Departments)
- POST Absen Masuk (Clock In)
- PUT Absen Keluar (Clock Out)
- GET Attendance Logs with:
  - Punctuality statuses for clock-in and clock-out based on department thresholds
  - Filters by date and departement

## Tech Stack

- Go 1.22+
- Gin Web Framework
- MySQL 5.7+/8.x
- database/sql with go-sql-driver/mysql

## Repository Layout

- Program entry: [main.go](/fleetify-backend/main.go)
- HTTP routes: [httpapi.SetupRoutes()](/fleetify-backend/internal/interfaces/http/router.go)
- Handlers:
  - [httpapi.EmployeeHandler](/fleetify-backend/internal/interfaces/http/employee_handler.go)
  - [httpapi.DepartementHandler](/fleetify-backend/internal/interfaces/http/departement_handler.go)
  - [httpapi.AttendanceHandler](/fleetify-backend/internal/interfaces/http/attendance_handler.go)
- Services:
  - [service.EmployeeService](/fleetify-backend/internal/app/service/employee_service.go)
  - [service.DepartementService](/fleetify-backend/internal/app/service/departement_service.go)
  - [service.AttendanceService](/fleetify-backend/internal/app/service/attendance_service.go)
- MySQL repositories:
  - [mysql.EmployeeMySQLRepository](/fleetify-backend/internal/infrastructure/repository/mysql/employee_repo.go)
  - [mysql.DepartementMySQLRepository](/fleetify-backend/internal/infrastructure/repository/mysql/departement_repo.go)
  - [mysql.AttendanceMySQLRepository](/fleetify-backend/internal/infrastructure/repository/mysql/attendance_repo.go)
- Domain contracts: [repository interfaces](/fleetify-backend/internal/domain/repository/repositories.go)

## Requirements

- Go 1.22+ installed
- MySQL server running and accessible
- Database created (default: fleetify_test)

## Configuration

The app reads configuration from environment variables:

- PORT: HTTP server port (default 8080)
- MYSQL_DSN: Full DSN; if empty, DSN is built from DB_* vars
- DB_USER, DB_PASS, DB_HOST, DB_PORT, DB_NAME

See [backend .env.example](/fleetify-backend/.env.example).

Example DSN: `root:@tcp(127.0.0.1:3306)/fleetify_test?parseTime=true&loc=Local`

## Running Locally

Windows CMD (adjust as needed):

1) Set environment:

- `set "PORT=8080"`
- `set "MYSQL_DSN=root:@tcp(127.0.0.1:3306)/fleetify_test?parseTime=true&loc=Local"`

1b) Prepare database (import sample schema and data):
- CLI (MySQL):
  ```
  mysql -u root -p -h 127.0.0.1 -P 3306 fleetify_test < fleetify_test.sql
  ```
- phpMyAdmin:
  - Buka database "fleetify_test"
  - Klik "Import" → pilih file [fleetify_test.sql](/fleetify_test.sql) → Start
- Dump berisi skema lengkap, index, foreign keys, dan data contoh untuk tabel departement, employee, attendance, attendance_history sesuai [fleetify_test.sql](/fleetify_test.sql).
2) Start server:

- `go run` [main.go](/fleetify-backend/main.go)

Server runs at `http://localhost:8080`.

## Build

- `go build -o fleetify-backend.exe` [main.go](/fleetify-backend/main.go)

## Database Notes

Expected tables:

- `departement` (id, departement_name, max_clock_in_time, max_clock_out_time)
- `employee` (id, employee_id, name, address, departement_id)
- `attendance` (id, attendance_id, employee_id, clock_in, clock_out)
- `attendance_history` (id, attendance_id, employee_id, date_attendance, attendance_type, description)

Relations:

- `employee.departement_id` → `departement.id`
- `attendance.employee_id` refers to `employee.employee_id`
- `attendance_history` tracks clock-in/out events (attendance_type 1=IN, 2=OUT)

## HTTP API Overview

Base URL: `http://localhost:8080/api`

### Departements

- GET `/departements` → list
- POST `/departements` → create
- GET `/departements/:id` → detail
- PUT `/departements/:id` → update
- DELETE `/departements/:id` → delete
  - Conflict (409) if employees still assigned to the departement
  - See handler: [httpapi.DepartementHandler.Delete()](/fleetify-backend/internal/interfaces/http/departement_handler.go)

### Employees

- GET `/employees?q=&departement_id=` → list with optional filters
- POST `/employees` → create
- GET `/employees/:id` → detail
- PUT `/employees/:id` → update
- DELETE `/employees/:id` → delete
  - Cascade supported when query `cascade=1` or header `X-Cascade: 1`
  - Non-cascade returns 409 when attendance records exist
  - See handler: [httpapi.EmployeeHandler.Delete()](/fleetify-backend/internal/interfaces/http/employee_handler.go)

### Attendance

- POST `/attendance/clock-in` → start attendance for today
- PUT `/attendance/clock-out` → close active attendance for today
- GET `/attendances/log?date=YYYY-MM-DD&departement_id=ID` → list logs

### Punctuality Logic

Implemented in [mysql.AttendanceMySQLRepository.QueryAttendanceLogs()](/fleetify-backend/internal/infrastructure/repository/mysql/attendance_repo.go):

Clock-in status:

- `TIME(clock_in) <= max_clock_in_time - 1h` → "Datang Lebih Cepat"
- `TIME(clock_in) <= max_clock_in_time` → "Tepat Waktu"
- `else` → "Terlambat"

Clock-out status:

- `clock_out IS NULL` → "Belum Clock Out"
- Within ±30m of `max_clock_out_time` → "Tepat Waktu"
- `< max_clock_out_time - 30m` → "Pulang Cepat"
- `> max_clock_out_time + 30m` → "Pulang Lambat"

### Error Semantics

- 400 Bad Request on validation errors
- 404 Not Found when resource missing or no active attendance for clock-out
- 409 Conflict for business rule violations (duplicate attendance, delete constraints)
- 500 Internal Server Error for unexpected failures

See handlers:

- [httpapi.AttendanceHandler.ClockIn()](/fleetify-backend/internal/interfaces/http/attendance_handler.go)
- [httpapi.AttendanceHandler.ClockOut()](/fleetify-backend/internal/interfaces/http/attendance_handler.go)
- [httpapi.EmployeeHandler.Delete()](/fleetify-backend/internal/interfaces/http/employee_handler.go)
- [httpapi.DepartementHandler.Delete()](/fleetify-backend/internal/interfaces/http/departement_handler.go)

## Example Requests

Create departement:

```
POST /api/departements
Content-Type: application/json

{
  "departement_name": "IT",
  "max_clock_in_time": "09:00:00",
  "max_clock_out_time": "18:00:00"
}
```

Clock in:

```
POST /api/attendance/clock-in
Content-Type: application/json

{"employee_id": "EMP001"}
```

Clock out:

```
PUT /api/attendance/clock-out
Content-Type: application/json

{"employee_id": "EMP001"}
```

Delete employee cascade:

```
DELETE /api/employees/123?cascade=1
X-Cascade: 1
```

## Architecture Notes

- Layered: handler → service → repository → MySQL
- Transactional operations via `database/sql` Tx
- Timezone: Asia/Jakarta preferred; fallback to WIB fixed zone in [service.AttendanceService](/fleetify-backend/internal/app/service/attendance_service.go)

## Security

- Never commit `.env`; use [.env.example](/fleetify-backend/.env.example)
- Validate all inputs in handlers
- Avoid leaking stack traces in error messages

## Testing

- Manual via curl/Postman
- Future work: add integration tests using a test DB

## Known Non-Functional Code

A GORM helper and models exist but are not used by the running backend:

- [database.ConnectMySQL()](/fleetify-backend/internal/infrastructure/database/mysql.go)
- [models.Attendance](/fleetify-backend/models/attendance.go)
- [models.Departement](fleetify-backend/models/departement.go:5)

These are legacy/experimental and can be removed or documented as optional.
