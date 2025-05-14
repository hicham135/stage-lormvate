<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;

// Main dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Team Management
Route::resource('team', TeamController::class);

// Attendance
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

// Tasks
Route::resource('tasks', TaskController::class);

// Evaluations
Route::resource('evaluations', EvaluationController::class);

// Reports
Route::resource('reports', ReportController::class);
Route::get('/reports/generate/monthly', [ReportController::class, 'generateMonthlyReport'])
     ->name('reports.generate.monthly');

// Requests
Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
Route::get('/requests/{id}', [RequestController::class, 'show'])->name('requests.show');
Route::post('/requests/{id}/approve', [RequestController::class, 'approve'])->name('requests.approve');
Route::post('/requests/{id}/reject', [RequestController::class, 'reject'])->name('requests.reject');

// Routes pour l'interface employé
Route::prefix('employee')->name('employee.')->group(function () {
    // Tableau de bord
    Route::get('/', [App\Http\Controllers\EmployeeDashboardController::class, 'index'])->name('dashboard');
    
    // Pointage de présence
    Route::get('/attendance', [App\Http\Controllers\EmployeeAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/check-in', [App\Http\Controllers\EmployeeAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [App\Http\Controllers\EmployeeAttendanceController::class, 'checkOut'])->name('attendance.check-out');
    Route::get('/attendance/history', [App\Http\Controllers\EmployeeAttendanceController::class, 'history'])->name('attendance.history');
    
    // Profil
    Route::get('/profile', [App\Http\Controllers\EmployeeProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [App\Http\Controllers\EmployeeProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\EmployeeProfileController::class, 'update'])->name('profile.update');
    
    // Tâches
    Route::get('/tasks', [App\Http\Controllers\EmployeeTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{id}', [App\Http\Controllers\EmployeeTaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{id}/status', [App\Http\Controllers\EmployeeTaskController::class, 'updateStatus'])->name('tasks.status');
    
    // Demandes
    Route::get('/requests', [App\Http\Controllers\EmployeeRequestController::class, 'index'])->name('requests.index');
    Route::get('/requests/create', [App\Http\Controllers\EmployeeRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests/store', [App\Http\Controllers\EmployeeRequestController::class, 'store'])->name('requests.store');
    Route::get('/requests/{id}', [App\Http\Controllers\EmployeeRequestController::class, 'show'])->name('requests.show');
    
    // Messages et annonces
    Route::get('/messages', [App\Http\Controllers\EmployeeMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{id}', [App\Http\Controllers\EmployeeMessageController::class, 'show'])->name('messages.show');
});