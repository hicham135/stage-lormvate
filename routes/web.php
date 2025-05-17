<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Auth\LoginController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirect root to login
Route::get('/', function () {
    return redirect('/login');
});

// Department Head Routes - Protected by auth middleware
Route::middleware(['auth'])->group(function () {
    // Dashboard for department head
    Route::get('/dashboard', function () {
        // Vérifier le rôle directement dans la route
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('employee.dashboard');
        }
        return app()->make(DashboardController::class)->index();
    })->name('dashboard');

    // Team Management
    Route::get('/team', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TeamController::class)->index();
    })->name('team.index');
    
    Route::get('/team/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TeamController::class)->show($id);
    })->name('team.show');
    
    Route::get('/team/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TeamController::class)->edit($id);
    })->name('team.edit');
    
    Route::put('/team/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TeamController::class)->update(request(), $id);
    })->name('team.update');

    // Attendance
    Route::get('/attendance', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(AttendanceController::class)->index(request());
    })->name('attendance.index');
    
    Route::get('/attendance/report', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(AttendanceController::class)->report(request());
    })->name('attendance.report');

    // Tasks
    Route::get('/tasks', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->index();
    })->name('tasks.index');
    
    Route::get('/tasks/create', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->create();
    })->name('tasks.create');
    
    Route::post('/tasks', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->store(request());
    })->name('tasks.store');
    
    Route::get('/tasks/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->show($id);
    })->name('tasks.show');
    
    Route::get('/tasks/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->edit($id);
    })->name('tasks.edit');
    
    Route::put('/tasks/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(TaskController::class)->update(request(), $id);
    })->name('tasks.update');

    // Evaluations
    Route::get('/evaluations', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->index();
    })->name('evaluations.index');
    
    Route::get('/evaluations/create', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->create(request());
    })->name('evaluations.create');
    
    Route::post('/evaluations', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->store(request());
    })->name('evaluations.store');
    
    Route::get('/evaluations/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->show($id);
    })->name('evaluations.show');
    
    Route::get('/evaluations/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->edit($id);
    })->name('evaluations.edit');
    
    Route::put('/evaluations/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(EvaluationController::class)->update(request(), $id);
    })->name('evaluations.update');

    // Reports
    Route::get('/reports', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->index();
    })->name('reports.index');
    
    Route::get('/reports/create', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->create();
    })->name('reports.create');
    
    Route::post('/reports', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->store(request());
    })->name('reports.store');
    
    Route::get('/reports/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->show($id);
    })->name('reports.show');
    
    Route::get('/reports/{id}/edit', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->edit($id);
    })->name('reports.edit');
    
    Route::put('/reports/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->update(request(), $id);
    })->name('reports.update');
    
    Route::get('/reports/generate/monthly', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(ReportController::class)->generateMonthlyReport();
    })->name('reports.generate.monthly');

    // Requests
    Route::get('/requests', function () {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(RequestController::class)->index();
    })->name('requests.index');
    
    Route::get('/requests/{id}', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(RequestController::class)->show($id);
    })->name('requests.show');
    
    Route::post('/requests/{id}/approve', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(RequestController::class)->approve($id);
    })->name('requests.approve');
    
    Route::post('/requests/{id}/reject', function ($id) {
        if (Auth::user()->role !== 'department_head') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(RequestController::class)->reject($id);
    })->name('requests.reject');
});

// Employee Routes - Protected by auth middleware
Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    // Tableau de bord
    Route::get('/', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('dashboard');
        }
        return app()->make(\App\Http\Controllers\EmployeeDashboardController::class)->index();
    })->name('dashboard');
    
    // Pointage de présence
    Route::get('/attendance', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeAttendanceController::class)->index();
    })->name('attendance.index');
    
    Route::post('/attendance/check-in', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeAttendanceController::class)->checkIn(request());
    })->name('attendance.check-in');
    
    Route::post('/attendance/check-out', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeAttendanceController::class)->checkOut(request());
    })->name('attendance.check-out');
    
    Route::get('/attendance/history', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeAttendanceController::class)->history(request());
    })->name('attendance.history');
    
    // Profil
    Route::get('/profile', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeProfileController::class)->index();
    })->name('profile.index');
    
    Route::get('/profile/edit', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeProfileController::class)->edit();
    })->name('profile.edit');
    
    Route::post('/profile/update', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeProfileController::class)->update(request());
    })->name('profile.update');
    
    // Tâches
    Route::get('/tasks', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeTaskController::class)->index(request());
    })->name('tasks.index');
    
    Route::get('/tasks/{id}', function ($id) {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeTaskController::class)->show($id);
    })->name('tasks.show');
    
    Route::post('/tasks/{id}/status', function ($id) {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeTaskController::class)->updateStatus(request(), $id);
    })->name('tasks.status');
    
    // Demandes
    Route::get('/requests', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeRequestController::class)->index();
    })->name('requests.index');
    
    Route::get('/requests/create', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeRequestController::class)->create();
    })->name('requests.create');
    
    Route::post('/requests/store', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeRequestController::class)->store(request());
    })->name('requests.store');
    
    Route::get('/requests/{id}', function ($id) {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeRequestController::class)->show($id);
    })->name('requests.show');
    
    // Messages et annonces
    Route::get('/messages', function () {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeMessageController::class)->index();
    })->name('messages.index');
    
    Route::get('/messages/{id}', function ($id) {
        if (Auth::user()->role !== 'employee') {
            return redirect()->route('login')->with('error', 'Accès non autorisé');
        }
        return app()->make(\App\Http\Controllers\EmployeeMessageController::class)->show($id);
    })->name('messages.show');
});