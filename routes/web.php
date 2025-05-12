<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartmentHeadController;

Route::middleware(['auth'])->group(function () {
    // Routes pour le Chef de département
    Route::middleware(['department.head'])->prefix('chef-departement')->name('department_head.')->group(function () {
        // Tableau de bord
        Route::get('/dashboard', [DepartmentHeadController::class, 'dashboard'])->name('dashboard');
        
        // Gestion de l'équipe
        Route::get('/equipe', [DepartmentHeadController::class, 'teamManagement'])->name('team_management');
        Route::get('/employe/{id}', [DepartmentHeadController::class, 'employeeDetails'])->name('employee_details');
        
        // Consultation des présences
        Route::get('/presences', [DepartmentHeadController::class, 'attendanceManagement'])->name('attendance');
        Route::get('/historique-presences', [DepartmentHeadController::class, 'attendanceHistory'])->name('attendance_history');
        
        // Validation des demandes
        Route::get('/demandes-conges', [DepartmentHeadController::class, 'leaveRequests'])->name('leave_requests');
        Route::post('/approuver-conge/{id}', [DepartmentHeadController::class, 'approveLeaveRequest'])->name('approve_leave');
        Route::post('/rejeter-conge/{id}', [DepartmentHeadController::class, 'rejectLeaveRequest'])->name('reject_leave');
        
        Route::get('/heures-supplementaires', [DepartmentHeadController::class, 'overtimeRequests'])->name('overtime_requests');
        Route::post('/approuver-heures/{id}', [DepartmentHeadController::class, 'approveOvertime'])->name('approve_overtime');
        
        // Attribution et suivi des tâches
        Route::get('/taches', [DepartmentHeadController::class, 'tasks'])->name('tasks');
        Route::post('/creer-tache', [DepartmentHeadController::class, 'createTask'])->name('create_task');
        Route::post('/update-tache/{id}', [DepartmentHeadController::class, 'updateTaskStatus'])->name('update_task');
        
        // Publication d'annonces départementales
        Route::get('/annonces', [DepartmentHeadController::class, 'announcements'])->name('announcements');
        Route::post('/creer-annonce', [DepartmentHeadController::class, 'createAnnouncement'])->name('create_announcement');
        Route::delete('/supprimer-annonce/{id}', [DepartmentHeadController::class, 'deleteAnnouncement'])->name('delete_announcement');
        
        // Évaluation des membres de l'équipe
        Route::get('/evaluations', [DepartmentHeadController::class, 'evaluations'])->name('evaluations');
        Route::post('/creer-evaluation', [DepartmentHeadController::class, 'createEvaluation'])->name('create_evaluation');
        Route::get('/edition-evaluation/{id}', [DepartmentHeadController::class, 'editEvaluation'])->name('edit_evaluation');
        Route::post('/update-evaluation/{id}', [DepartmentHeadController::class, 'updateEvaluation'])->name('update_evaluation');
        
        // Rapports départementaux
        Route::get('/rapports', [DepartmentHeadController::class, 'reports'])->name('reports');
        Route::post('/generer-rapport', [DepartmentHeadController::class, 'generateReport'])->name('generate_report');
        Route::get('/voir-rapport/{id}', [DepartmentHeadController::class, 'viewReport'])->name('view_report');
    });

    // Add this to your routes/web.php file


// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Registration Routes
Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

// Password Reset Routes
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
