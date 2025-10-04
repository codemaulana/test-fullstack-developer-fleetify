package mysql

import (
	"database/sql"
	"fmt"
	"strings"
	"time"

	repo "fleetify-backend/internal/domain/repository"
)

type EmployeeMySQLRepository struct {
	db *sql.DB
}

func NewEmployeeMySQLRepository(db *sql.DB) *EmployeeMySQLRepository {
	return &EmployeeMySQLRepository{db: db}
}

func (r *EmployeeMySQLRepository) List() ([]repo.Employee, error) {
	const query = `
		SELECT
			e.id,
			COALESCE(e.employee_id, '') AS employee_id,
			COALESCE(e.name, '') AS name,
			COALESCE(e.address, '') AS address,
			COALESCE(e.departement_id, 0) AS departement_id,
			d.departement_name,
			CAST(COALESCE(e.created_at, NOW()) AS DATETIME) AS created_at,
			CAST(COALESCE(e.updated_at, NOW()) AS DATETIME) AS updated_at
		FROM employee e
		LEFT JOIN departement d ON e.departement_id = d.id`
	rows, err := r.db.Query(query)
	if err != nil {
		return nil, fmt.Errorf("list employees: %w", err)
	}
	defer rows.Close()

	var employees []repo.Employee
	for rows.Next() {
		var emp repo.Employee
		var createdAt time.Time
		var updatedAt time.Time
		if err := rows.Scan(
			&emp.ID,
			&emp.EmployeeID,
			&emp.Name,
			&emp.Address,
			&emp.DepartementID,
			&emp.DepartementName,
			&createdAt,
			&updatedAt,
		); err != nil {
			return nil, fmt.Errorf("scan employee: %w", err)
		}
		emp.CreatedAt = createdAt
		emp.UpdatedAt = updatedAt
		employees = append(employees, emp)
	}

	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("rows error: %w", err)
	}

	return employees, nil
}

func (r *EmployeeMySQLRepository) Create(payload repo.EmployeeCreate) (int64, error) {
	const query = "INSERT INTO employee (employee_id, name, address, departement_id) VALUES (?, ?, ?, ?)"
	result, err := r.db.Exec(query, payload.EmployeeID, payload.Name, payload.Address, payload.DepartementID)
	if err != nil {
		return 0, fmt.Errorf("create employee: %w", err)
	}
	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("lastInsertId employee: %w", err)
	}
	return id, nil
}

func (r *EmployeeMySQLRepository) GetByID(id int64) (repo.Employee, error) {
	const query = `
		SELECT
			e.id,
			COALESCE(e.employee_id, '') AS employee_id,
			COALESCE(e.name, '') AS name,
			COALESCE(e.address, '') AS address,
			COALESCE(e.departement_id, 0) AS departement_id,
			d.departement_name,
			CAST(COALESCE(e.created_at, NOW()) AS DATETIME) AS created_at,
			CAST(COALESCE(e.updated_at, NOW()) AS DATETIME) AS updated_at
		FROM employee e
		LEFT JOIN departement d ON e.departement_id = d.id
		WHERE e.id = ?`

	var emp repo.Employee
	var createdAt time.Time
	var updatedAt time.Time
	err := r.db.QueryRow(query, id).Scan(
		&emp.ID,
		&emp.EmployeeID,
		&emp.Name,
		&emp.Address,
		&emp.DepartementID,
		&emp.DepartementName,
		&createdAt,
		&updatedAt,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return repo.Employee{}, sql.ErrNoRows
		}
		return repo.Employee{}, fmt.Errorf("get employee by id: %w", err)
	}
	emp.CreatedAt = createdAt
	emp.UpdatedAt = updatedAt
	return emp, nil
}

func (r *EmployeeMySQLRepository) Update(id int64, payload repo.EmployeeCreate) error {
	const query = "UPDATE employee SET employee_id = ?, name = ?, address = ?, departement_id = ? WHERE id = ?"
	if _, err := r.db.Exec(query, payload.EmployeeID, payload.Name, payload.Address, payload.DepartementID, id); err != nil {
		return fmt.Errorf("update employee: %w", err)
	}
	return nil
}

func (r *EmployeeMySQLRepository) Delete(id int64) error {
	const query = "DELETE FROM employee WHERE id = ?"
	if _, err := r.db.Exec(query, id); err != nil {
		return fmt.Errorf("delete employee: %w", err)
	}
	return nil
}



func (r *EmployeeMySQLRepository) DeleteCascadeByID(id int64) error {
	tx, err := r.db.Begin()
	if err != nil {
		return fmt.Errorf("begin tx: %w", err)
	}
	defer func() { _ = tx.Rollback() }()

	const selEmpID = "SELECT employee_id FROM employee WHERE id = ?"
	var empID string
	if err := tx.QueryRow(selEmpID, id).Scan(&empID); err != nil {
		if err == sql.ErrNoRows {
			return sql.ErrNoRows
		}
		return fmt.Errorf("get employee_id: %w", err)
	}

	if _, err := tx.Exec("DELETE FROM attendance_history WHERE employee_id = ?", empID); err != nil {
		return fmt.Errorf("delete attendance_history: %w", err)
	}

	if _, err := tx.Exec("DELETE FROM attendance WHERE employee_id = ?", empID); err != nil {
		return fmt.Errorf("delete attendance: %w", err)
	}

	// Finally delete the employee row
	if _, err := tx.Exec("DELETE FROM employee WHERE id = ?", id); err != nil {
		return fmt.Errorf("delete employee: %w", err)
	}

	if err := tx.Commit(); err != nil {
		return fmt.Errorf("commit tx: %w", err)
	}
	return nil
}

func (r *EmployeeMySQLRepository) ListByFilters(q string, departementID string) ([]repo.Employee, error) {
	baseQuery := `
		SELECT
			e.id,
			COALESCE(e.employee_id, '') AS employee_id,
			COALESCE(e.name, '') AS name,
			COALESCE(e.address, '') AS address,
			COALESCE(e.departement_id, 0) AS departement_id,
			d.departement_name,
			CAST(COALESCE(e.created_at, NOW()) AS DATETIME) AS created_at,
			CAST(COALESCE(e.updated_at, NOW()) AS DATETIME) AS updated_at
		FROM employee e
		LEFT JOIN departement d ON e.departement_id = d.id
	`

	filters := []string{}
	args := []interface{}{}

	if q != "" {
		like := "%" + q + "%"
		filters = append(filters, "(e.name LIKE ? OR e.employee_id LIKE ? OR d.departement_name LIKE ?)")
		args = append(args, like, like, like)
	}

	if departementID != "" {
		filters = append(filters, "e.departement_id = ?")
		args = append(args, departementID)
	}

	if len(filters) > 0 {
		baseQuery += " WHERE " + strings.Join(filters, " AND ")
	}

	rows, err := r.db.Query(baseQuery, args...)
	if err != nil {
		return nil, fmt.Errorf("list employees by filters: %w", err)
	}
	defer rows.Close()

	var employees []repo.Employee
	for rows.Next() {
		var emp repo.Employee
		var createdAt time.Time
		var updatedAt time.Time
		if err := rows.Scan(
			&emp.ID,
			&emp.EmployeeID,
			&emp.Name,
			&emp.Address,
			&emp.DepartementID,
			&emp.DepartementName,
			&createdAt,
			&updatedAt,
		); err != nil {
			return nil, fmt.Errorf("scan employee: %w", err)
		}
		emp.CreatedAt = createdAt
		emp.UpdatedAt = updatedAt
		employees = append(employees, emp)
	}

	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("rows error: %w", err)
	}

	return employees, nil
}