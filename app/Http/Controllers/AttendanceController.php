<?php
namespace App\Http\Controllers;
//app/Http/Controllers/AttendanceController.php

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        $departmentId = $departmentHead->department_id;
        
        $date = $request->input('date', Carbon::today()->toDateString());
        
        $attendances = Attendance::whereHas('user', function($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('date', $date)
            ->get();
        
        // Get team members who don't have attendance for this date
        $teamWithoutAttendance = User::where('department_id', $departmentId)
            ->whereNotIn('id', $attendances->pluck('user_id'))
            ->get();
        
        return view('attendance.index', compact('attendances', 'teamWithoutAttendance', 'date'));
    }
    
    public function report(Request $request)
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        $departmentId = $departmentHead->department_id;
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $attendanceReport = User::where('department_id', $departmentId)
            ->with(['attendances' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            }])
            ->get();
        
        return view('attendance.report', compact('attendanceReport', 'startDate', 'endDate'));
    }
}