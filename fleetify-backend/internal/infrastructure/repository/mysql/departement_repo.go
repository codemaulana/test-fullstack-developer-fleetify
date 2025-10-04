package mysql

import (
	"database/sql"
	"fmt"

	repo "fleetify-backend/internal/domain/repository"
)

type DepartementMySQLRepository struct {
	db *sql.DB
}

func NewDepartementMySQLRepository(db *sql.DB) *DepartementMySQLRepository {
	return &DepartementMySQLRepository{db: db}
}

func (r *DepartementMySQLRepository) List() ([]repo.Departement, error) {
	const query = "SELECT id, departement_name, max_clock_in_time, max_clock_out_time FROM departement"
	rows, err := r.db.Query(query)
	if err != nil {
		return nil, fmt.Errorf("list departements: %w", err)
	}
	defer rows.Close()

	var departements []repo.Departement
	for rows.Next() {
		var d repo.Departement
		if err := rows.Scan(&d.ID, &d.DepartementName, &d.MaxClockInTime, &d.MaxClockOutTime); err != nil {
			return nil, fmt.Errorf("scan departement: %w", err)
		}
		departements = append(departements, d)
	}
	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("rows error: %w", err)
	}
	return departements, nil
}

func (r *DepartementMySQLRepository) Create(dept repo.Departement) (int64, error) {
	const query = "INSERT INTO departement (departement_name, max_clock_in_time, max_clock_out_time) VALUES (?, ?, ?)"
	result, err := r.db.Exec(query, dept.DepartementName, dept.MaxClockInTime, dept.MaxClockOutTime)
	if err != nil {
		return 0, fmt.Errorf("create departement: %w", err)
	}
	id, err := result.LastInsertId()
	if err != nil {
		return 0, fmt.Errorf("lastInsertId departement: %w", err)
	}
	return id, nil
}

func (r *DepartementMySQLRepository) GetByID(id int64) (repo.Departement, error) {
	const query = "SELECT id, departement_name, max_clock_in_time, max_clock_out_time FROM departement WHERE id = ?"
	var d repo.Departement
	if err := r.db.QueryRow(query, id).Scan(&d.ID, &d.DepartementName, &d.MaxClockInTime, &d.MaxClockOutTime); err != nil {
		if err == sql.ErrNoRows {
			return repo.Departement{}, sql.ErrNoRows
		}
		return repo.Departement{}, fmt.Errorf("get departement by id: %w", err)
	}
	return d, nil
}

func (r *DepartementMySQLRepository) Update(id int64, dept repo.Departement) error {
	const query = "UPDATE departement SET departement_name = ?, max_clock_in_time = ?, max_clock_out_time = ? WHERE id = ?"
	if _, err := r.db.Exec(query, dept.DepartementName, dept.MaxClockInTime, dept.MaxClockOutTime, id); err != nil {
		return fmt.Errorf("update departement: %w", err)
	}
	return nil
}

func (r *DepartementMySQLRepository) Delete(id int64) error {
	const check = "SELECT COUNT(*) FROM employee WHERE departement_id = ?"
	var cnt int64
	if err := r.db.QueryRow(check, id).Scan(&cnt); err != nil {
		return fmt.Errorf("check employees for departement: %w", err)
	}
	if cnt > 0 {
		return fmt.Errorf("cannot delete departement: %d employees still assigned", cnt)
	}

	const query = "DELETE FROM departement WHERE id = ?"
	if _, err := r.db.Exec(query, id); err != nil {
		return fmt.Errorf("delete departement: %w", err)
	}
	return nil
}