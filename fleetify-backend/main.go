package main

import (
	"database/sql"
	"log"
	"os"
	"time"

	"github.com/gin-gonic/gin"
	_ "github.com/go-sql-driver/mysql"

	"fleetify-backend/internal/app/service"
	mysqlrepo "fleetify-backend/internal/infrastructure/repository/mysql"
	httpapi "fleetify-backend/internal/interfaces/http"
)

func mustEnv(key, def string) string {
	v := os.Getenv(key)
	if v == "" {
		return def
	}
	return v
}

func main() {
	router := gin.Default()

	_ = router.SetTrustedProxies(nil)

	dsn := mustEnv("MYSQL_DSN", "")
	if dsn == "" {
		user := mustEnv("DB_USER", "root")
		pass := mustEnv("DB_PASS", "")
		host := mustEnv("DB_HOST", "127.0.0.1")
		port := mustEnv("DB_PORT", "3306")
		name := mustEnv("DB_NAME", "fleetify_test")
		dsn = user + ":" + pass + "@tcp(" + host + ":" + port + ")/" + name + "?parseTime=true&loc=Local"
	}

	db, err := sql.Open("mysql", dsn)
	if err != nil {
		log.Fatalf("open mysql: %v", err)
	}
	db.SetConnMaxLifetime(1 * time.Hour)
	db.SetMaxOpenConns(50)
	db.SetMaxIdleConns(5)

	if err := db.Ping(); err != nil {
		log.Fatalf("ping mysql: %v", err)
	}

	empRepo := mysqlrepo.NewEmployeeMySQLRepository(db)
	deptRepo := mysqlrepo.NewDepartementMySQLRepository(db)
	attRepo := mysqlrepo.NewAttendanceMySQLRepository(db)

	empSvc := service.NewEmployeeService(empRepo)
	deptSvc := service.NewDepartementService(deptRepo)
	attSvc := service.NewAttendanceService(attRepo)

	// Handlers
	empHandler := httpapi.NewEmployeeHandler(empSvc)
	deptHandler := httpapi.NewDepartementHandler(deptSvc)
	attHandler := httpapi.NewAttendanceHandler(attSvc)

	// Routes
	httpapi.SetupRoutes(router, empHandler, deptHandler, attHandler)

	// Start HTTP server
	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}
	if err := router.Run(":" + port); err != nil {
		log.Fatal(err)
	}
}