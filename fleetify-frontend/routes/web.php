<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartementController;
use Illuminate\Support\Facades\Route;

// Jadikan route utama ke dashboard
Route::get('/', function () {
    return redirect()->route('attendance.dashboard');
});

// CRUD Karyawan
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employees.update');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

// CRUD Departemen
Route::get('/departements', [DepartementController::class, 'index'])->name('departements.index');
Route::get('/departements/create', [DepartementController::class, 'create'])->name('departements.create');
Route::post('/departements', [DepartementController::class, 'store'])->name('departements.store');
Route::get('/departements/{id}/edit', [DepartementController::class, 'edit'])->name('departements.edit');
Route::put('/departements/{id}', [DepartementController::class, 'update'])->name('departements.update');
Route::delete('/departements/{id}', [DepartementController::class, 'destroy'])->name('departements.destroy');

// Log Absensi (filter tanggal dan departemen)
Route::get('/attendance-log', [AttendanceController::class, 'index'])->name('attendance.log');

// Dashboard + Aksi Absen
Route::get('/dashboard', [AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
Route::post('/attendance/action', [AttendanceController::class, 'storeAction'])->name('attendance.action');