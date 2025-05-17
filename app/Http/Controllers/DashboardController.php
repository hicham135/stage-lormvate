<?php
// app/Http/Controllers/DashboardController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\User;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Request as DepartmentRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // For now, we'll simulate a department head
        // In a real app, you'd use Auth::user()
        $departmentHead = User::where('role', 'department_head')->first();
        $departmentId = $departmentHead->department_id;
        
        $department = Department::findOrFail($departmentId);
        $totalEmployees = User::where('department_id', $departmentId)->count();
        $pendingTasks = Task::where('department_id', $departmentId)
                            ->where('status', 'pending')
                            ->count();
        $pendingRequests = DepartmentRequest::where('department_id', $departmentId)
                                 ->where('status', 'pending')
                                 ->count();
        
        return view('dashboard', compact(
            'department', 
            'totalEmployees', 
            'pendingTasks', 
            'pendingRequests'
        ));
    }
}
