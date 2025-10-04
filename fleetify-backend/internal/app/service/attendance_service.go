package service

import (
	"database/sql"
	"fmt"
	"time"

	repo "fleetify-backend/internal/domain/repository"
)

type AttendanceService struct {
	repo repo.AttendanceRepository
}

func NewAttendanceService(r repo.AttendanceRepository) *AttendanceService {
	return &AttendanceService{repo: r}
}

func (s *AttendanceService) ClockIn(employeeID string) error {
	if _, err := s.repo.FindOpenAttendanceID(employeeID); err == nil {
		return fmt.Errorf("active attendance exists")
	} else if err != sql.ErrNoRows {
		return fmt.Errorf("query error: %w", err)
	}
	hasToday, err := s.repo.HasTodayAttendance(employeeID, time.Now().In(time.FixedZone("WIB", 7*60*60)))
	if err != nil {
		return fmt.Errorf("query error: %w", err)
	}
	if hasToday {
		return fmt.Errorf("kehadiran sudah dicatat hari ini")
	}

	tx, err := s.repo.BeginTx()
	if err != nil {
		return fmt.Errorf("begin tx: %w", err)
	}
	defer func() {
		_ = tx.Rollback()
	}()

	loc, err := time.LoadLocation("Asia/Jakarta")
	if err != nil {
		loc = time.FixedZone("WIB", 7*60*60)
	}
	now := time.Now().In(loc)
	attendanceIDStr := fmt.Sprintf("ATT-%s-%s", employeeID, now.Format("20060102"))

	_, err = s.repo.InsertAttendanceTx(tx, repo.Attendance{
		EmployeeID:   employeeID,
		AttendanceID: attendanceIDStr,
		ClockIn:      now,
	})
	if err != nil {
		return fmt.Errorf("insert attendance: %w", err)
	}

	if err := s.repo.InsertAttendanceHistoryTx(tx, repo.AttendanceHistory{
		AttendanceID:   attendanceIDStr, 
		EmployeeID:     employeeID,
		DateAttendance: now,
		AttendanceType: 1,
		Description:    "",
	}); err != nil {
		return fmt.Errorf("insert attendance history: %w", err)
	}

	if err := tx.Commit(); err != nil {
		return fmt.Errorf("commit tx: %w", err)
	}
	return nil
}

func (s *AttendanceService) ClockOut(employeeID string) error {
	attID, err := s.repo.FindOpenAttendanceID(employeeID)
	if err != nil {
		if err == sql.ErrNoRows {
			return sql.ErrNoRows
		}
		return fmt.Errorf("find active attendance: %w", err)
	}

	tx, err := s.repo.BeginTx()
	if err != nil {
		return fmt.Errorf("begin tx: %w", err)
	}
	defer func() {
		_ = tx.Rollback()
	}()

	loc, err := time.LoadLocation("Asia/Jakarta")
	if err != nil {
		loc = time.FixedZone("WIB", 7*60*60)
	}
	now := time.Now().In(loc)
	if err := s.repo.UpdateAttendanceClockOutTx(tx, attID, now); err != nil {
		return fmt.Errorf("update clock_out: %w", err)
	}

	attStringID, err := s.repo.GetAttendanceStringIDByPK(attID)
	if err != nil {
		return fmt.Errorf("get attendance string id: %w", err)
	}
	if err := s.repo.InsertAttendanceHistoryTx(tx, repo.AttendanceHistory{
		AttendanceID:   attStringID,
		EmployeeID:     employeeID,
		DateAttendance: now,
		AttendanceType: 2,
		Description:    "",
	}); err != nil {
		return fmt.Errorf("insert attendance history: %w", err)
	}

	if err := tx.Commit(); err != nil {
		return fmt.Errorf("commit tx: %w", err)
	}
	return nil
}

func (s *AttendanceService) GetAttendanceLogs(date string, departementID string) (*sql.Rows, error) {
	return s.repo.QueryAttendanceLogs(date, departementID)
}