package models

import "time"

type Departement struct {
	ID          uint      `gorm:"primaryKey"`
	Name        string    `gorm:"size:100;not null;unique"`
	Description string    `gorm:"size:255"`
	CreatedAt   time.Time
	UpdatedAt   time.Time
	Employees   []Employee `gorm:"foreignKey:DepartementID"`
}