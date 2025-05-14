<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Request as EmployeeRequest;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        // Pour la démo, on simule un employé connecté
        // Dans une vraie app, on utiliserait Auth::user()
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $pendingTasks = Task::where('assigned_to', $employee->id)
                           ->whereIn('status', ['pending', 'in_progress'])
                           ->count();
        
        $completedTasks = Task::where('assigned_to', $employee->id)
                             ->where('status', 'completed')
                             ->count();
        
        $pendingRequests = EmployeeRequest::where('user_id', $employee->id)
                                      ->where('status', 'pending')
                                      ->count();
        
        $todayAttendance = Attendance::where('user_id', $employee->id)
                                    ->where('date', now()->toDateString())
                                    ->first();
        
        $recentMessages = Message::where('department_id', $employee->department_id)
                                ->orWhere('user_id', $employee->id)
                                ->orderBy('created_at', 'desc')
                                ->take(5)
                                ->get();
        
        return view('employee.dashboard', compact(
            'employee', 
            'pendingTasks', 
            'completedTasks', 
            'pendingRequests', 
            'todayAttendance',
            'recentMessages'
        ));
    }
}