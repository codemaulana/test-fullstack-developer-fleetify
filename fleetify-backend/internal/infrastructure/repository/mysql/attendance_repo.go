package mysql

import (
	"database/sql"
	"fmt"
	"strings"
	"time"

	repo "fleetify-backend/internal/domain/repository"
)

type AttendanceMySQLRepository struct {
	db *sql.DB
}

func NewAttendanceMySQLRepository(db *sql.DB) *AttendanceMySQLRepository {
	return &AttendanceMySQLRepository{db: db}
}

func (r *AttendanceMySQLRepository) FindOpenAttendanceID(employeeID string) (int64, error) {
	const query = "SELECT id FROM attendance WHERE employee_id = ? AND clock_out IS NULL ORDER BY clock_in DESC LIMIT 1"
	var id int64
	err := r.db.QueryRow(query, employeeID).Scan(&id)
	if err != nil {
		return 0, err
	}
	return id, nil
}

func (r *AttendanceMySQLRepository) HasTodayAttendance(employeeID string, date time.Time) (bool, error) {
	const query = "SELECT COUNT(*) FROM attendance WHERE employee_id = ? AND DATE(clock_in) = ?"
	var count int64
	if err := r.db.QueryRow(query, employeeID, date.Format("2006-01-02")).Scan(&count); err != nil {
		return false, err
	}
	return count > 0, nil
}

func (r *AttendanceMySQLRepository) BeginTx() (*sql.Tx, error) {
	return r.db.Begin()
}

func (r *AttendanceMySQLRepository) InsertAttendanceTx(tx *sql.Tx, att repo.Attendance) (int64, error) {
	const query = "INSERT INTO attendance (employee_id, attendance_id, clock_in) VALUES (?, ?, ?)"
	result, err := tx.Exec(query, att.EmployeeID, att.AttendanceID, att.ClockIn)
	if err != nil {
		return 0, fmt.Errorf("insert attendance: %w", err)
	}
	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("lastInsertId attendance: %w", err)
	}
	return id, nil
}

func (r *AttendanceMySQLRepository) UpdateAttendanceClockOutTx(tx *sql.Tx, attendanceID int64, clockOut time.Time) error {
	const query = "UPDATE attendance SET clock_out = ? WHERE id = ?"
	if _, err := tx.Exec(query, clockOut, attendanceID); err != nil {
		return fmt.Errorf("update clock_out: %w", err)
	}
	return nil
}

func (r *AttendanceMySQLRepository) InsertAttendanceHistoryTx(tx *sql.Tx, h repo.AttendanceHistory) error {
	const query = "INSERT INTO attendance_history (attendance_id, employee_id, date_attendance, attendance_type, description) VALUES (?, ?, ?, ?, ?)"
	if _, err := tx.Exec(query, h.AttendanceID, h.EmployeeID, h.DateAttendance, h.AttendanceType, h.Description); err != nil {
		return fmt.Errorf("insert attendance_history: %w", err)
	}
	return nil
}

func (r *AttendanceMySQLRepository) GetAttendanceStringIDByPK(attendancePK int64) (string, error) {
	const query = "SELECT attendance_id FROM attendance WHERE id = ?"
	var attendanceID string
	if err := r.db.QueryRow(query, attendancePK).Scan(&attendanceID); err != nil {
		return "", err
	}
	return attendanceID, nil
}

func (r *AttendanceMySQLRepository) QueryAttendanceLogs(date string, departementID string) (*sql.Rows, error) {
	baseQuery := `
		SELECT 
			e.employee_id, 
			e.name, 
			d.departement_name, 
			a.clock_in, 
			a.clock_out,
			CASE
				WHEN TIME(a.clock_in) <= SUBTIME(d.max_clock_in_time, '01:00:00') THEN 'Datang Lebih Cepat'
				WHEN TIME(a.clock_in) <= d.max_clock_in_time THEN 'Tepat Waktu'
				ELSE 'Terlambat'
			END AS clock_in_status,
			CASE
				WHEN a.clock_out IS NULL THEN 'Belum Clock Out'
				WHEN TIME(a.clock_out) BETWEEN SUBTIME(d.max_clock_out_time, '00:30:00') AND ADDTIME(d.max_clock_out_time, '00:30:00') THEN 'Tepat Waktu'
				WHEN TIME(a.clock_out) < SUBTIME(d.max_clock_out_time, '00:30:00') THEN 'Pulang Cepat'
				WHEN TIME(a.clock_out) > ADDTIME(d.max_clock_out_time, '00:30:00') THEN 'Pulang Lambat'
				ELSE 'Tepat Waktu'
			END AS clock_out_status
		FROM attendance a
		JOIN employee e ON a.employee_id = e.employee_id
		JOIN departement d ON e.departement_id = d.id
	`

	filters := []string{}
	args := []interface{}{}

	if date != "" {
		filters = append(filters, "DATE(a.clock_in) = ?")
		args = append(args, date)
	}

	if departementID != "" {
		filters = append(filters, "d.id = ?")
		args = append(args, departementID)
	}

	if len(filters) > 0 {
		baseQuery += " WHERE " + strings.Join(filters, " AND ")
	}

	baseQuery += " ORDER BY a.clock_in DESC"

	rows, err := r.db.Query(baseQuery, args...)
	if err != nil {
		return nil, fmt.Errorf("query attendance logs: %w", err)
	}
	return rows, nil
}