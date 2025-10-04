package httpapi

import (
	"net/http"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
	"fleetify-backend/internal/app/service"
	repo "fleetify-backend/internal/domain/repository"
)

type DepartementHandler struct {
	svc *service.DepartementService
}

func NewDepartementHandler(s *service.DepartementService) *DepartementHandler {
	return &DepartementHandler{svc: s}
}

type departementCreateRequest struct {
	DepartementName string `json:"departement_name" binding:"required"`
	MaxClockInTime  string `json:"max_clock_in_time" binding:"required"`
	MaxClockOutTime string `json:"max_clock_out_time" binding:"required"`
}

type departementOut struct {
	ID              int64  `json:"id"`
	DepartementName string `json:"departement_name"`
	MaxClockInTime  string `json:"max_clock_in_time"`
	MaxClockOutTime string `json:"max_clock_out_time"`
}

// List all departements
func (h *DepartementHandler) List(c *gin.Context) {
	items, err := h.svc.ListDepartements()
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil daftar departemen"})
		return
	}
	out := make([]departementOut, 0, len(items))
	for _, d := range items {
		out = append(out, departementOut{
			ID:              d.ID,
			DepartementName: d.DepartementName,
			MaxClockInTime:  d.MaxClockInTime,
			MaxClockOutTime: d.MaxClockOutTime,
		})
	}
	c.JSON(http.StatusOK, gin.H{"data": out})
}

// Create a new departement
func (h *DepartementHandler) Create(c *gin.Context) {
	var req departementCreateRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid"})
		return
	}

	id, err := h.svc.CreateDepartement(repo.Departement{
		DepartementName: req.DepartementName,
		MaxClockInTime:  req.MaxClockInTime,
		MaxClockOutTime: req.MaxClockOutTime,
	})
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal membuat departemen"})
		return
	}

	c.JSON(http.StatusCreated, gin.H{"data": departementOut{
		ID:              id,
		DepartementName: req.DepartementName,
		MaxClockInTime:  req.MaxClockInTime,
		MaxClockOutTime: req.MaxClockOutTime,
	}})
}

// Get departement by ID
func (h *DepartementHandler) GetByID(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	d, err := h.svc.GetDepartementByID(id)
	if err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal mengambil data departemen"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"data": departementOut{
		ID:              d.ID,
		DepartementName: d.DepartementName,
		MaxClockInTime:  d.MaxClockInTime,
		MaxClockOutTime: d.MaxClockOutTime,
	}})
}

// Update departement
func (h *DepartementHandler) Update(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	var req departementCreateRequest
	if err := c.ShouldBindJSON(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Input tidak valid"})
		return
	}

	if err := h.svc.UpdateDepartement(id, repo.Departement{
		ID:              id,
		DepartementName: req.DepartementName,
		MaxClockInTime:  req.MaxClockInTime,
		MaxClockOutTime: req.MaxClockOutTime,
	}); err != nil {
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal memperbarui departemen"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Departemen berhasil diperbarui"})
}

// Delete departement
func (h *DepartementHandler) Delete(c *gin.Context) {
	idStr := c.Param("id")
	id, err := strconv.ParseInt(idStr, 10, 64)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "ID tidak valid"})
		return
	}

	if err := h.svc.DeleteDepartement(id); err != nil {
		if strings.Contains(err.Error(), "cannot delete departement") {
			c.JSON(http.StatusConflict, gin.H{
				"error": "Departemen tidak bisa dihapus karena masih ada karyawan yang ter-assign.",
				"hint":  "Silakan pindahkan (reassign) atau hapus karyawan tersebut terlebih dahulu.",
			})
			return
		}
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Gagal menghapus departemen"})
		return
	}

	c.JSON(http.StatusOK, gin.H{"message": "Departemen berhasil dihapus"})
}