package models

import "time"

type Attendance struct {
	ID         uint        `gorm:"primaryKey"`
	EmployeeID uint        `gorm:"index;not null"`
	Employee   Employee    `gorm:"constraint:OnUpdate:CASCADE,OnDelete:SET NULL;"`
	CheckIn    *time.Time
	CheckOut   *time.Time
	CreatedAt  time.Time
	UpdatedAt  time.Time
}