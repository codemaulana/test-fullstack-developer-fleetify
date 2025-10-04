package repository

import (
	"database/sql"
	"time"
)

type Employee struct {
	ID              int64
	EmployeeID      string
	Name            string
	Address         string
	DepartementID   int64
	DepartementName sql.NullString
	CreatedAt       time.Time
	UpdatedAt       time.Time
}

type EmployeeCreate struct {
	EmployeeID    string
	Name          string
	Address       string
	DepartementID int64
}

type Departement struct {
	ID              int64
	DepartementName string
	MaxClockInTime  string
	MaxClockOutTime string
}

type Attendance struct {
	ID           int64
	EmployeeID   string
	AttendanceID string
	ClockIn      time.Time
	ClockOut     sql.NullTime
}

type AttendanceHistory struct {
	ID             int64
	AttendanceID   string
	EmployeeID     string
	DateAttendance time.Time
	AttendanceType int
	Description    string
}

type EmployeeRepository interface {
	List() ([]Employee, error)
	ListByFilters(q string, departementID string) ([]Employee, error)
	Create(payload EmployeeCreate) (int64, error)
	GetByID(id int64) (Employee, error)
	Update(id int64, payload EmployeeCreate) error
	Delete(id int64) error
	DeleteCascadeByID(id int64) error
}

type DepartementRepository interface {
	List() ([]Departement, error)
	Create(dept Departement) (int64, error)
	GetByID(id int64) (Departement, error)
	Update(id int64, dept Departement) error
	Delete(id int64) error
}

type AttendanceRepository interface {
	FindOpenAttendanceID(employeeID string) (int64, error)

	HasTodayAttendance(employeeID string, date time.Time) (bool, error)

	BeginTx() (*sql.Tx, error)
	InsertAttendanceTx(tx *sql.Tx, att Attendance) (int64, error)
	UpdateAttendanceClockOutTx(tx *sql.Tx, attendanceID int64, clockOut time.Time) error
	InsertAttendanceHistoryTx(tx *sql.Tx, history AttendanceHistory) error

	GetAttendanceStringIDByPK(attendancePK int64) (string, error)

	QueryAttendanceLogs(date string, departementID string) (*sql.Rows, error)
}