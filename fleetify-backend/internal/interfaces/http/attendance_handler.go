package httpapi

import (
	"database/sql"
	"errors"
	"net/http"
	"strings"

	"github.com/gin-gonic/gin"
	"fleetify-backend/internal/app/service"
)

type AttendanceHandler struct {
	svc *service.AttendanceService
}

func NewAttendanceHandler(s *service.AttendanceService) *AttendanceHandler {
	return &AttendanceHandler{svc: s}
}

type attendanceActionRequest struct {
	EmployeeID string `json:"employee_id" binding:"required"`
}

func (h *AttendanceHandler) ClockIn(c *gin.Context) {
	var req attendanceActionRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid: " + err.Error()})
		return
	}

	if err := h.svc.ClockIn(req.EmployeeID); err != nil {
		msg := err.Error()
		if strings.Contains(msg, "active attendance exists") ||
			strings.Contains(msg, "sudah melakukan clock in") {
			c.JSON(http.StatusConflict, gin.H{"error": "Anda sudah melakukan clock in dan belum clock out."})
			return
		}
		if strings.Contains(msg, "kehadiran sudah dicatat hari ini") ||
			strings.Contains(msg, "attendance already recorded today") {
			c.JSON(http.StatusConflict, gin.H{"error": "Anda sudah melakukan absen hari ini."})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mencatat clock in"})
		return
	}

	c.JSON(http.StatusCreated, gin.H{"message": "Clock in berhasil"})
}

func (h *AttendanceHandler) ClockOut(c *gin.Context) {
	var req attendanceActionRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid: " + err.Error()})
		return
	}

	if err := h.svc.ClockOut(req.EmployeeID); err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			c.JSON(http.StatusNotFound, gin.H{"error": "Tidak ditemukan sesi clock in aktif untuk hari ini"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mencatat clock out"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Clock out berhasil"})
}

type attendanceLog struct {
	EmployeeID      string `json:"employee_id"`
	EmployeeName    string `json:"employee_name"`
	DepartementName string `json:"departement_name"`
	ClockIn         string `json:"clock_in"`
	ClockOut        string `json:"clock_out"`
	ClockInStatus   string `json:"clock_in_status"`
	ClockOutStatus  string `json:"clock_out_status"`
}

func (h *AttendanceHandler) GetAttendanceLog(c *gin.Context) {
	date := c.Query("date")
	deptID := c.Query("departement_id")

	rows, err := h.svc.GetAttendanceLogs(date, deptID)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menjalankan query: " + err.Error()})
		return
	}
	defer rows.Close()

	var logs []attendanceLog
	for rows.Next() {
		var (
			empID, empName, clockIn, clockInStatus, clockOutStatus string
			deptName                                                sql.NullString
			clockOut                                                sql.NullString
		)
		if err := rows.Scan(
			&empID,
			&empName,
			&deptName,
			&clockIn,
			&clockOut,
			&clockInStatus,
			&clockOutStatus,
		); err != nil {
			c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal membaca baris data: " + err.Error()})
			return
		}
		logs = append(logs, attendanceLog{
			EmployeeID:      empID,
			EmployeeName:    empName,
			DepartementName: nullString(deptName),
			ClockIn:         clockIn,
			ClockOut:        nullString(clockOut),
			ClockInStatus:   clockInStatus,
			ClockOutStatus:  clockOutStatus,
		})
	}

	c.JSON(http.StatusOK, gin.H{"data": logs})
}

func nullString(ns sql.NullString) string {
	if ns.Valid {
		return ns.String
	}
	return ""
}