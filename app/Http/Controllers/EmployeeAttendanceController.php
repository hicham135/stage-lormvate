<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeAttendanceController extends Controller
{
    public function index()
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $todayAttendance = Attendance::where('user_id', $employee->id)
                                    ->where('date', now()->toDateString())
                                    ->first();
        
        return view('employee.attendance.index', compact('employee', 'todayAttendance'));
    }
    
    public function checkIn(Request $request)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $todayAttendance = Attendance::where('user_id', $employee->id)
                                    ->where('date', now()->toDateString())
                                    ->first();
        
        if (!$todayAttendance) {
            $todayAttendance = new Attendance();
            $todayAttendance->user_id = $employee->id;
            $todayAttendance->date = now()->toDateString();
            $todayAttendance->status = 'present';
        }
        
        $todayAttendance->check_in = now();
        $todayAttendance->save();
        
        return redirect()->route('employee.attendance.index')
                         ->with('success', 'Vous avez pointé votre arrivée avec succès.');
    }
    
    public function checkOut(Request $request)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $todayAttendance = Attendance::where('user_id', $employee->id)
                                    ->where('date', now()->toDateString())
                                    ->first();
        
        if ($todayAttendance && $todayAttendance->check_in) {
            $todayAttendance->check_out = now();
            $todayAttendance->save();
            
            return redirect()->route('employee.attendance.index')
                             ->with('success', 'Vous avez pointé votre départ avec succès.');
        }
        
        return redirect()->route('employee.attendance.index')
                         ->with('error', 'Vous devez d\'abord pointer votre arrivée.');
    }
    
    public function history(Request $request)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        
        $attendanceHistory = Attendance::where('user_id', $employee->id)
                                      ->whereBetween('date', [$startDate, $endDate])
                                      ->orderBy('date', 'desc')
                                      ->get();
        
        return view('employee.attendance.history', compact('employee', 'attendanceHistory', 'startDate', 'endDate'));
    }
}
