<?php
// app/Http/Controllers/DepartmentHeadController.php
namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\User;
use App\Models\Task;
use App\Models\Announcement;
use App\Models\Evaluation;
use App\Models\LeaveRequest;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DepartmentHeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('department.head');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        // Statistiques du département
        $employeeCount = User::where('department_id', $department->id)->count();
        $presentToday = Attendance::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->whereDate('check_in', Carbon::today())
        ->count();
        
        $pendingLeaveRequests = LeaveRequest::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->where('status', 'pending')
        ->count();
        
        $pendingTasks = Task::where('department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        return view('department_head.dashboard', compact(
            'user', 
            'department', 
            'employeeCount', 
            'presentToday',
            'pendingLeaveRequests',
            'pendingTasks'
        ));
    }

    // Gestion de l'équipe
    public function teamManagement()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        $employees = User::where('department_id', $department->id)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();
        
        return view('department_head.team_management', compact('department', 'employees'));
    }
    
    public function employeeDetails($id)
    {
        $user = Auth::user();
        $employee = User::findOrFail($id);
        
        // Vérification que l'employé est dans le département du chef
        if ($employee->department_id !== $user->department_id) {
            return redirect()->route('department_head.team_management')
                ->with('error', 'Cet employé n\'appartient pas à votre département.');
        }
        
        $attendances = Attendance::where('user_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(30)
            ->get();
            
        $tasks = Task::where('assigned_to', $employee->id)
            ->orderBy('due_date')
            ->get();
            
        $evaluations = Evaluation::where('evaluatee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $leaveRequests = LeaveRequest::where('user_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('department_head.employee_details', compact(
            'employee', 
            'attendances', 
            'tasks', 
            'evaluations',
            'leaveRequests'
        ));
    }

    // Consultation des présences
    public function attendanceManagement()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $today = Carbon::today();
        $attendances = Attendance::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->whereDate('check_in', $today)
        ->with('user')
        ->get();
        
        $employees = User::where('department_id', $department->id)->get();
        $presentEmployeeIds = $attendances->pluck('user_id')->toArray();
        $absentEmployees = $employees->filter(function ($employee) use ($presentEmployeeIds) {
            return !in_array($employee->id, $presentEmployeeIds);
        });
        
        return view('department_head.attendance', compact(
            'department', 
            'attendances', 
            'absentEmployees', 
            'today'
        ));
    }
    
    public function attendanceHistory(Request $request)
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $employeeId = $request->input('employee_id');
        
        $query = Attendance::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->whereBetween('check_in', [$startDate, $endDate]);
        
        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }
        
        $attendances = $query->with('user')->orderBy('check_in', 'desc')->paginate(20);
        $employees = User::where('department_id', $department->id)->orderBy('name')->get();
        
        return view('department_head.attendance_history', compact(
            'department', 
            'attendances', 
            'employees', 
            'startDate', 
            'endDate', 
            'employeeId'
        ));
    }

    // Validation des demandes
    public function leaveRequests()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $pendingRequests = LeaveRequest::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->where('status', 'pending')
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();
        
        $processsedRequests = LeaveRequest::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->whereIn('status', ['approved', 'rejected'])
        ->with(['user', 'approver'])
        ->orderBy('updated_at', 'desc')
        ->take(20)
        ->get();
        
        return view('department_head.leave_requests', compact(
            'department', 
            'pendingRequests', 
            'processsedRequests'
        ));
    }
    
    public function approveLeaveRequest($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Vérification que la demande vient d'un employé du département du chef
        $employee = User::findOrFail($leaveRequest->user_id);
        if ($employee->department_id !== $user->department_id) {
            return redirect()->route('department_head.leave_requests')
                ->with('error', 'Cette demande ne provient pas d\'un employé de votre département.');
        }
        
        $leaveRequest->status = 'approved';
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->save();
        
        return redirect()->route('department_head.leave_requests')
            ->with('success', 'Demande de congé approuvée avec succès.');
    }
    
    public function rejectLeaveRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::findOrFail($id);
        
        // Vérification que la demande vient d'un employé du département du chef
        $employee = User::findOrFail($leaveRequest->user_id);
        if ($employee->department_id !== $user->department_id) {
            return redirect()->route('department_head.leave_requests')
                ->with('error', 'Cette demande ne provient pas d\'un employé de votre département.');
        }
        
        $leaveRequest->status = 'rejected';
        $leaveRequest->approved_by = $user->id;
        $leaveRequest->notes = $request->input('rejection_reason');
        $leaveRequest->save();
        
        return redirect()->route('department_head.leave_requests')
            ->with('success', 'Demande de congé rejetée.');
    }
    
    public function overtimeRequests()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $pendingOvertimes = Attendance::whereHas('user', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })
        ->where('overtime_hours', '>', 0)
        ->where('overtime_approved', false)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();
        
        return view('department_head.overtime_requests', compact('department', 'pendingOvertimes'));
    }
    
    public function approveOvertime($id)
    {
        $attendance = Attendance::findOrFail($id);
        $user = Auth::user();
        
        // Vérification que l'employé est dans le département du chef
        $employee = User::findOrFail($attendance->user_id);
        if ($employee->department_id !== $user->department_id) {
            return redirect()->route('department_head.overtime_requests')
                ->with('error', 'Cet employé n\'appartient pas à votre département.');
        }
        
        $attendance->overtime_approved = true;
        $attendance->save();
        
        return redirect()->route('department_head.overtime_requests')
            ->with('success', 'Heures supplémentaires approuvées.');
    }

    // Attribution et suivi des tâches
    public function tasks()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $pendingTasks = Task::where('department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->with(['assignee', 'assigner'])
            ->orderBy('due_date')
            ->get();
            
        $completedTasks = Task::where('department_id', $department->id)
            ->where('status', 'completed')
            ->with(['assignee', 'assigner'])
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();
            
        $employees = User::where('department_id', $department->id)
            ->orderBy('name')
            ->get();
        
        return view('department_head.tasks', compact(
            'department', 
            'pendingTasks', 
            'completedTasks', 
            'employees'
        ));
    }
    
    public function createTask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after_or_equal:today',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        // Vérification que l'employé assigné est dans le département du chef
        $assignee = User::findOrFail($request->input('assigned_to'));
        if ($assignee->department_id !== $user->department_id) {
            return redirect()->route('department_head.tasks')
                ->with('error', 'Cet employé n\'appartient pas à votre département.');
        }
        
        Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'assigned_by' => $user->id,
            'assigned_to' => $request->input('assigned_to'),
            'department_id' => $department->id,
            'due_date' => $request->input('due_date'),
            'priority' => $request->input('priority'),
            'status' => 'pending',
        ]);
        
        return redirect()->route('department_head.tasks')
            ->with('success', 'Tâche créée avec succès.');
    }
    
    public function updateTaskStatus(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::findOrFail($id);
        
        // Vérification que la tâche est dans le département du chef
        if ($task->department_id !== $user->department_id) {
            return redirect()->route('department_head.tasks')
                ->with('error', 'Cette tâche n\'appartient pas à votre département.');
        }
        
        $task->status = $request->input('status');
        if ($request->has('progress')) {
            $task->progress = $request->input('progress');
        }
        
        if ($request->input('status') === 'completed') {
            $task->progress = 100;
        }
        
        $task->save();
        
        return redirect()->route('department_head.tasks')
            ->with('success', 'Statut de la tâche mis à jour.');
    }

    // Publication d'annonces départementales
    public function announcements()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $activeAnnouncements = Announcement::where(function ($query) use ($department) {
                $query->where('department_id', $department->id)
                    ->orWhere('is_global', true);
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', Carbon::now());
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $expiredAnnouncements = Announcement::where('department_id', $department->id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', Carbon::now())
            ->with('user')
            ->orderBy('expires_at', 'desc')
            ->take(10)
            ->get();
        
        return view('department_head.announcements', compact(
            'department', 
            'activeAnnouncements', 
            'expiredAnnouncements'
        ));
    }
    
    public function createAnnouncement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_pinned' => 'boolean',
            'expires_at' => 'nullable|date|after:today',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        Announcement::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => $user->id,
            'department_id' => $department->id,
            'is_global' => false,
            'is_pinned' => $request->has('is_pinned'),
            'expires_at' => $request->input('expires_at'),
        ]);
        
        return redirect()->route('department_head.announcements')
            ->with('success', 'Annonce publiée avec succès.');
    }
    
    public function deleteAnnouncement($id)
    {
        $user = Auth::user();
        $announcement = Announcement::findOrFail($id);
        
        // Vérification que l'annonce appartient au département du chef
        if ($announcement->department_id !== $user->department_id || $announcement->user_id !== $user->id) {
            return redirect()->route('department_head.announcements')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette annonce.');
        }
        
        $announcement->delete();
        
        return redirect()->route('department_head.announcements')
            ->with('success', 'Annonce supprimée avec succès.');
    }

    // Évaluation des membres de l'équipe
    public function evaluations()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $pendingEvaluations = Evaluation::where('evaluator_id', $user->id)
            ->where('status', 'draft')
            ->with('evaluatee')
            ->orderBy('created_at', 'desc')
            ->get();
            
        $completedEvaluations = Evaluation::where('evaluator_id', $user->id)
            ->whereIn('status', ['submitted', 'acknowledged'])
            ->with('evaluatee')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();
            
        $employees = User::where('department_id', $department->id)
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();
        
        return view('department_head.evaluations', compact(
            'department', 
            'pendingEvaluations', 
            'completedEvaluations', 
            'employees'
        ));
    }
    
    public function createEvaluation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'evaluatee_id' => 'required|exists:users,id',
            'period' => 'required|string|max:50',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        
        // Vérification que l'employé évalué est dans le département du chef
        $evaluatee = User::findOrFail($request->input('evaluatee_id'));
        if ($evaluatee->department_id !== $user->department_id) {
            return redirect()->route('department_head.evaluations')
                ->with('error', 'Cet employé n\'appartient pas à votre département.');
        }
        
        $evaluation = Evaluation::create([
            'evaluator_id' => $user->id,
            'evaluatee_id' => $request->input('evaluatee_id'),
            'period' => $request->input('period'),
            'status' => 'draft',
        ]);
        
        return redirect()->route('department_head.edit_evaluation', ['id' => $evaluation->id])
            ->with('success', 'Évaluation créée. Veuillez la compléter.');
    }
    
    public function editEvaluation($id)
    {
        $user = Auth::user();
        $evaluation = Evaluation::findOrFail($id);
        
        // Vérification que l'évaluation a été créée par le chef
        if ($evaluation->evaluator_id !== $user->id) {
            return redirect()->route('department_head.evaluations')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette évaluation.');
        }
        
        $evaluatee = User::findOrFail($evaluation->evaluatee_id);
        
        return view('department_head.edit_evaluation', compact('evaluation', 'evaluatee'));
    }
    
    public function updateEvaluation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'performance_score' => 'required|integer|min:1|max:5',
            'punctuality_score' => 'required|integer|min:1|max:5',
            'teamwork_score' => 'required|integer|min:1|max:5',
            'initiative_score' => 'required|integer|min:1|max:5',
            'comments' => 'required|string',
            'goals' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $evaluation = Evaluation::findOrFail($id);
        
        // Vérification que l'évaluation a été créée par le chef
        if ($evaluation->evaluator_id !== $user->id) {
            return redirect()->route('department_head.evaluations')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette évaluation.');
        }
        
        $evaluation->performance_score = $request->input('performance_score');
        $evaluation->punctuality_score = $request->input('punctuality_score');
        $evaluation->teamwork_score = $request->input('teamwork_score');
        $evaluation->initiative_score = $request->input('initiative_score');
        $evaluation->comments = $request->input('comments');
        $evaluation->goals = $request->input('goals');
        
        if ($request->has('submit')) {
            $evaluation->status = 'submitted';
        }
        
        $evaluation->save();
        
        return redirect()->route('department_head.evaluations')
            ->with('success', 'Évaluation mise à jour avec succès.');
    }

    // Rapports départementaux
    public function reports()
    {
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        $reports = Report::where('department_id', $department->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('department_head.reports', compact('department', 'reports'));
    }
    
    public function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:attendance,performance,tasks,general',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = Auth::user();
        $department = Department::findOrFail($user->department_id);
        
        // Logique pour générer le rapport en fonction du type
        $reportData = [];
        $fileName = '';
        
        switch ($request->input('type')) {
            case 'attendance':
                $reportData = $this->generateAttendanceReport($department->id, $request->input('period_start'), $request->input('period_end'));
                $fileName = 'attendance_report_' . time() . '.pdf';
                break;
                
            case 'performance':
                $reportData = $this->generatePerformanceReport($department->id, $request->input('period_start'), $request->input('period_end'));
                $fileName = 'performance_report_' . time() . '.pdf';
                break;
                
            case 'tasks':
                $reportData = $this->generateTasksReport($department->id, $request->input('period_start'), $request->input('period_end'));
                $fileName = 'tasks_report_' . time() . '.pdf';
                break;
                
            default:
                $reportData = $this->generateGeneralReport($department->id, $request->input('period_start'), $request->input('period_end'));
                $fileName = 'general_report_' . time() . '.pdf';
                break;
        }
        
        // Dans une vraie application, on générerait le fichier PDF ici
        // Pour l'instant, on simule juste la création du rapport
        
        $report = Report::create([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'department_id' => $department->id,
            'period_start' => $request->input('period_start'),
            'period_end' => $request->input('period_end'),
            'generated_by' => $user->id,
            'file_path' => 'reports/' . $fileName,
            'parameters' => [
                'filters' => $request->except(['_token', 'name', 'type', 'period_start', 'period_end']),
            ],
        ]);
        
        return redirect()->route('department_head.view_report', ['id' => $report->id])
            ->with('success', 'Rapport généré avec succès.');
    }
    
    public function viewReport($id)
    {
        $user = Auth::user();
        $report = Report::findOrFail($id);
        
        // Vérification que le rapport appartient au département du chef
        if ($report->department_id !== $user->department_id) {
            return redirect()->route('department_head.reports')
                ->with('error', 'Vous n\'êtes pas autorisé à voir ce rapport.');
        }
        
        $department = Department::findOrFail($user->department_id);
        $reportData = [];
        
        switch ($report->type) {
            case 'attendance':
                $reportData = $this->generateAttendanceReport($department->id, $report->period_start, $report->period_end);
                break;
                
            case 'performance':
                $reportData = $this->generatePerformanceReport($department->id, $report->period_start, $report->period_end);
                break;
                
            case 'tasks':
                $reportData = $this->generateTasksReport($department->id, $report->period_start, $report->period_end);
                break;
                
            default:
                $reportData = $this->generateGeneralReport($department->id, $report->period_start, $report->period_end);
                break;
        }
        
        return view('department_head.view_report', compact('department', 'report', 'reportData'));
    }
    
    // Méthodes privées pour la génération de rapports
    private function generateAttendanceReport($departmentId, $startDate, $endDate)
    {
        $employees = User::where('department_id', $departmentId)->get();
        $attendanceData = [];
        
        foreach ($employees as $employee) {
            $attendances = Attendance::where('user_id', $employee->id)
                ->whereBetween('check_in', [$startDate, $endDate])
                ->get();
                
            $presentDays = $attendances->where('status', 'present')->count();
            $lateDays = $attendances->where('status', 'late')->count();
            $absentDays = $attendances->where('status', 'absent')->count();
            $leaveDays = $attendances->where('status', 'leave')->count();
            
            $totalHours = 0;
            $overtimeHours = 0;
            
            foreach ($attendances as $attendance) {
                if ($attendance->check_in && $attendance->check_out) {
                    $hours = $attendance->check_in->diffInHours($attendance->check_out);
                    $totalHours += $hours;
                    $overtimeHours += $attendance->overtime_hours;
                }
            }
            
            $attendanceData[] = [
                'employee' => $employee,
                'present_days' => $presentDays,
                'late_days' => $lateDays,
                'absent_days' => $absentDays,
                'leave_days' => $leaveDays,
                'total_hours' => $totalHours,
                'overtime_hours' => $overtimeHours,
            ];
        }
        
        return [
            'title' => 'Rapport de présence',
            'period' => 'Du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            'data' => $attendanceData,
        ];
    }
    
    private function generatePerformanceReport($departmentId, $startDate, $endDate)
    {
        $employees = User::where('department_id', $departmentId)->get();
        $performanceData = [];
        
        foreach ($employees as $employee) {
            $evaluations = Evaluation::where('evaluatee_id', $employee->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'submitted')
                ->get();
                
            if ($evaluations->count() > 0) {
                $avgPerformance = $evaluations->avg('performance_score');
                $avgPunctuality = $evaluations->avg('punctuality_score');
                $avgTeamwork = $evaluations->avg('teamwork_score');
                $avgInitiative = $evaluations->avg('initiative_score');
                $avgTotal = ($avgPerformance + $avgPunctuality + $avgTeamwork + $avgInitiative) / 4;
                
                $performanceData[] = [
                    'employee' => $employee,
                    'avg_performance' => round($avgPerformance, 1),
                    'avg_punctuality' => round($avgPunctuality, 1),
                    'avg_teamwork' => round($avgTeamwork, 1),
                    'avg_initiative' => round($avgInitiative, 1),
                    'avg_total' => round($avgTotal, 1),
                    'evaluations_count' => $evaluations->count(),
                ];
            }
        }
        
        return [
            'title' => 'Rapport de performance',
            'period' => 'Du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            'data' => $performanceData,
        ];
    }
    
    private function generateTasksReport($departmentId, $startDate, $endDate)
    {
        $employees = User::where('department_id', $departmentId)->get();
        $tasksData = [];
        
        foreach ($employees as $employee) {
            $tasks = Task::where('assigned_to', $employee->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();
                
            if ($tasks->count() > 0) {
                $completedTasks = $tasks->where('status', 'completed')->count();
                $pendingTasks = $tasks->where('status', 'pending')->count();
                $inProgressTasks = $tasks->where('status', 'in_progress')->count();
                $delayedTasks = $tasks->where('status', 'delayed')->count();
                
                $urgentTasks = $tasks->where('priority', 'urgent')->count();
                $highTasks = $tasks->where('priority', 'high')->count();
                $mediumTasks = $tasks->where('priority', 'medium')->count();
                $lowTasks = $tasks->where('priority', 'low')->count();
                
                $completionRate = $tasks->count() > 0 ? ($completedTasks / $tasks->count()) * 100 : 0;
                
                $tasksData[] = [
                    'employee' => $employee,
                    'total_tasks' => $tasks->count(),
                    'completed_tasks' => $completedTasks,
                    'pending_tasks' => $pendingTasks,
                    'in_progress_tasks' => $inProgressTasks,
                    'delayed_tasks' => $delayedTasks,
                    'urgent_tasks' => $urgentTasks,
                    'high_tasks' => $highTasks,
                    'medium_tasks' => $mediumTasks,
                    'low_tasks' => $lowTasks,
                    'completion_rate' => round($completionRate, 1),
                ];
            }
        }
        
        return [
            'title' => 'Rapport des tâches',
            'period' => 'Du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            'data' => $tasksData,
        ];
    }
    
    private function generateGeneralReport($departmentId, $startDate, $endDate)
    {
        $department = Department::findOrFail($departmentId);
        $employeeCount = User::where('department_id', $departmentId)->count();
        
        $attendances = Attendance::whereHas('user', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereBetween('check_in', [$startDate, $endDate])
        ->get();
        
        $tasks = Task::where('department_id', $departmentId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
            
        $leaveRequests = LeaveRequest::whereHas('user', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();
        
        $evaluations = Evaluation::whereHas('evaluatee', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get();
        
        return [
            'title' => 'Rapport général du département',
            'period' => 'Du ' . Carbon::parse($startDate)->format('d/m/Y') . ' au ' . Carbon::parse($endDate)->format('d/m/Y'),
            'department' => $department,
            'employee_count' => $employeeCount,
            'attendance_stats' => [
                'total' => $attendances->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'leave' => $attendances->where('status', 'leave')->count(),
            ],
            'task_stats' => [
                'total' => $tasks->count(),
                'completed' => $tasks->where('status', 'completed')->count(),
                'pending' => $tasks->where('status', 'pending')->count(),
                'in_progress' => $tasks->where('status', 'in_progress')->count(),
                'delayed' => $tasks->where('status', 'delayed')->count(),
            ],
            'leave_stats' => [
                'total' => $leaveRequests->count(),
                'approved' => $leaveRequests->where('status', 'approved')->count(),
                'rejected' => $leaveRequests->where('status', 'rejected')->count(),
                'pending' => $leaveRequests->where('status', 'pending')->count(),
            ],
            'evaluation_stats' => [
                'total' => $evaluations->count(),
                'avg_performance' => $evaluations->count() > 0 ? round($evaluations->avg('performance_score'), 1) : 0,
                'avg_punctuality' => $evaluations->count() > 0 ? round($evaluations->avg('punctuality_score'), 1) : 0,
                'avg_teamwork' => $evaluations->count() > 0 ? round($evaluations->avg('teamwork_score'), 1) : 0,
                'avg_initiative' => $evaluations->count() > 0 ? round($evaluations->avg('initiative_score'), 1) : 0,
            ],
        ];
    }
}