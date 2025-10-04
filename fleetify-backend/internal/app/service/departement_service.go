package service

import (
	repo "fleetify-backend/internal/domain/repository"
)

type DepartementService struct {
	repo repo.DepartementRepository
}

func NewDepartementService(r repo.DepartementRepository) *DepartementService {
	return &DepartementService{repo: r}
}

func (s *DepartementService) ListDepartements() ([]repo.Departement, error) {
	return s.repo.List()
}

func (s *DepartementService) CreateDepartement(d repo.Departement) (int64, error) {
	return s.repo.Create(d)
}

func (s *DepartementService) GetDepartementByID(id int64) (repo.Departement, error) {
	return s.repo.GetByID(id)
}

func (s *DepartementService) UpdateDepartement(id int64, d repo.Departement) error {
	return s.repo.Update(id, d)
}

func (s *DepartementService) DeleteDepartement(id int64) error {
	return s.repo.Delete(id)
}