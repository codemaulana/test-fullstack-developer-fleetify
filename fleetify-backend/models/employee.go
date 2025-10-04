package models

import "time"

type Employee struct {
	ID            uint         `gorm:"primaryKey"`
	DepartementID uint         `gorm:"index"`
	Departement   Departement  `gorm:"constraint:OnUpdate:CASCADE,OnDelete:SET NULL;"`
	Name          string       `gorm:"size:100;not null"`
	Email         string       `gorm:"size:120;unique;not null"`
	Position      string       `gorm:"size:100"`
	CreatedAt     time.Time
	UpdatedAt     time.Time
}