package httpapi

import "github.com/gin-gonic/gin"

func SetupRoutes(router *gin.Engine, eh *EmployeeHandler, dh *DepartementHandler, ah *AttendanceHandler) {
	api := router.Group("/api")
	{
		// Departement Routes
		api.GET("/departements", dh.List)
		api.POST("/departements", dh.Create)
		api.GET("/departements/:id", dh.GetByID)
		api.PUT("/departements/:id", dh.Update)
		api.DELETE("/departements/:id", dh.Delete)

		// Employee Routes
		api.GET("/employees", eh.List)
		api.POST("/employees", eh.Create)
		api.GET("/employees/:id", eh.GetByID)
		api.PUT("/employees/:id", eh.Update)
		api.DELETE("/employees/:id", eh.Delete)

		// Attendance Routes
		api.POST("/attendance/clock-in", ah.ClockIn)
		api.PUT("/attendance/clock-out", ah.ClockOut)
		api.GET("/attendances/log", ah.GetAttendanceLog)
	}
}