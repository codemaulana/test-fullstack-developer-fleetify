package service

import (
	repo "fleetify-backend/internal/domain/repository"
)

type EmployeeService struct {
	repo repo.EmployeeRepository
}

func NewEmployeeService(r repo.EmployeeRepository) *EmployeeService {
	return &EmployeeService{repo: r}
}

func (s *EmployeeService) ListEmployees() ([]repo.Employee, error) {
	return s.repo.List()
}

func (s *EmployeeService) ListEmployeesByFilters(q string, departementID string) ([]repo.Employee, error) {
	return s.repo.ListByFilters(q, departementID)
}

func (s *EmployeeService) CreateEmployee(payload repo.EmployeeCreate) (int64, error) {
	return s.repo.Create(payload)
}

func (s *EmployeeService) GetEmployeeByID(id int64) (repo.Employee, error) {
	return s.repo.GetByID(id)
}

func (s *EmployeeService) UpdateEmployee(id int64, payload repo.EmployeeCreate) error {
	return s.repo.Update(id, payload)
}

func (s *EmployeeService) DeleteEmployee(id int64) error {
	return s.repo.Delete(id)
}

// DeleteEmployeeCascade removes the employee and all related attendance (+ history) in a single transaction.
func (s *EmployeeService) DeleteEmployeeCascade(id int64) error {
	return s.repo.DeleteCascadeByID(id)
}

