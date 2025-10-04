package httpapi

import (
	"database/sql"
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"fleetify-backend/internal/app/service"
	repo "fleetify-backend/internal/domain/repository"
)

type EmployeeHandler struct {
	svc *service.EmployeeService
}

func NewEmployeeHandler(s *service.EmployeeService) *EmployeeHandler {
	return &EmployeeHandler{svc: s}
}

type employeeCreateRequest struct {
	EmployeeID    string `json:"employee_id" binding:"required"`
	Name          string `json:"name" binding:"required"`
	Address       string `json:"address" binding:"required"`
	DepartementID int64  `json:"departement_id" binding:"required"`
}

type employeeOut struct {
	ID              int64           `json:"id"`
	EmployeeID      string          `json:"employee_id"`
	Name            string          `json:"name"`
	Address         string          `json:"address"`
	DepartementID   int64           `json:"departement_id"`
	DepartementName sql.NullString  `json:"departement_name"`
	CreatedAt       string          `json:"created_at"`
	UpdatedAt       string          `json:"updated_at"`
}

func mapRepoEmployeeToOut(e repo.Employee) employeeOut {
	return employeeOut{
		ID:              e.ID,
		EmployeeID:      e.EmployeeID,
		Name:            e.Name,
		Address:         e.Address,
		DepartementID:   e.DepartementID,
		DepartementName: e.DepartementName,
		CreatedAt:       e.CreatedAt.Format("2006-01-02 15:04:05"),
		UpdatedAt:       e.UpdatedAt.Format("2006-01-02 15:04:05"),
	}
}

// List supports optional filters: q (name/employee_id/departement_name) and departement_id
func (h *EmployeeHandler) List(c *gin.Context) {
	q := c.Query("q")
	deptID := c.Query("departement_id")

	var (
		items []repo.Employee
		err   error
	)
	if q != "" || deptID != "" {
		items, err = h.svc.ListEmployeesByFilters(q, deptID)
	} else {
		items, err = h.svc.ListEmployees()
	}
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil daftar karyawan: " + err.Error()})
		return
	}

	out := make([]employeeOut, 0, len(items))
	for _, it := range items {
		out = append(out, mapRepoEmployeeToOut(it))
	}
	c.JSON(http.StatusOK, gin.H{"data": out})
}

func (h *EmployeeHandler) Create(c *gin.Context) {
	var req employeeCreateRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid: " + err.Error()})
		return
	}

	id, err := h.svc.CreateEmployee(repo.EmployeeCreate{
		EmployeeID:    req.EmployeeID,
		Name:          req.Name,
		Address:       req.Address,
		DepartementID: req.DepartementID,
	})
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal membuat karyawan"})
		return
	}

	c.JSON(http.StatusCreated, gin.H{"message": "Karyawan berhasil dibuat", "id": id})
}

func (h *EmployeeHandler) GetByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	emp, err := h.svc.GetEmployeeByID(id)
	if err != nil {
		if err == sql.ErrNoRows {
			c.JSON(http.StatusNotFound, gin.H{"error": "Karyawan tidak ditemukan"})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil data karyawan"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"data": mapRepoEmployeeToOut(emp)})
}

func (h *EmployeeHandler) Update(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	var req employeeCreateRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid: " + err.Error()})
		return
	}

	if err := h.svc.UpdateEmployee(id, repo.EmployeeCreate{
		EmployeeID:    req.EmployeeID,
		Name:          req.Name,
		Address:       req.Address,
		DepartementID: req.DepartementID,
	}); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal memperbarui data karyawan"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Data karyawan berhasil diperbarui"})
}

func (h *EmployeeHandler) Delete(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	rawCascade := c.Query("cascade")
	if rawCascade == "" {
		rawCascade = c.PostForm("cascade")
	}
	if rawCascade == "" {
		rawCascade = c.GetHeader("X-Cascade")
	}
	cascadeVal := strings.TrimSpace(rawCascade)
	isCascade := cascadeVal != "" &&
		(cascadeVal == "1" ||
			strings.EqualFold(cascadeVal, "true") ||
			strings.EqualFold(cascadeVal, "on") ||
			strings.EqualFold(cascadeVal, "yes"))
	var delErr error
	if isCascade {
		delErr = h.svc.DeleteEmployeeCascade(id)
	} else {
		delErr = h.svc.DeleteEmployee(id)
	}

	if delErr != nil {
		if delErr == sql.ErrNoRows {
			c.JSON(http.StatusNotFound, gin.H{"error": "Karyawan tidak ditemukan"})
			return
		}
		msg := delErr.Error()
		if strings.Contains(msg, "foreign key") ||
			strings.Contains(msg, "cannot delete employee") ||
			strings.Contains(msg, "Cannot delete or update a parent row") ||
			strings.Contains(msg, "a foreign key constraint fails") ||
			strings.Contains(msg, "constraint fails") {
			c.JSON(http.StatusConflict, gin.H{
				"error": "Tidak bisa menghapus karyawan karena masih memiliki data absensi.",
				"hint":  "Hapus/arsipkan log absensi karyawan ini terlebih dahulu atau gunakan opsi hapus beserta log (cascade).",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghapus data karyawan"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Data karyawan berhasil dihapus"})
}