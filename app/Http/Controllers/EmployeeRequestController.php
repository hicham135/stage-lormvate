<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as EmployeeRequest;
use Illuminate\Support\Facades\Auth;

class EmployeeRequestController extends Controller
{
    public function index()
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $requests = EmployeeRequest::where('user_id', $employee->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('employee.requests.index', compact('employee', 'requests'));
    }
    
    public function create()
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        return view('employee.requests.create', compact('employee'));
    }
    
    public function store(Request $request)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'type' => 'required|in:leave,expense,equipment,other',
        ]);
        
        $employeeRequest = new EmployeeRequest();
        $employeeRequest->title = $request->title;
        $employeeRequest->description = $request->description;
        $employeeRequest->type = $request->type;
        $employeeRequest->status = 'pending';
        $employeeRequest->user_id = $employee->id;
        $employeeRequest->department_id = $employee->department_id;
        $employeeRequest->save();
        
        return redirect()->route('employee.requests.index')
                         ->with('success', 'Demande soumise avec succès.');
    }
    
    public function show($id)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $employeeRequest = EmployeeRequest::findOrFail($id);
        
        // Vérifier si la demande appartient à cet employé
        if ($employeeRequest->user_id != $employee->id) {
            return redirect()->route('employee.requests.index')
                             ->with('error', 'Vous n\'êtes pas autorisé à voir cette demande.');
        }
        
        return view('employee.requests.show', compact('employee', 'employeeRequest'));
    }
}