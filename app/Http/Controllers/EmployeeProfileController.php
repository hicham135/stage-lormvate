<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;

class EmployeeProfileController extends Controller
{
    public function index()
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $evaluations = Evaluation::where('evaluated_user_id', $employee->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('employee.profile.index', compact('employee', 'evaluations'));
    }
    
    public function edit()
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        return view('employee.profile.edit', compact('employee'));
    }
    
    public function update(Request $request)
    {
        // Simuler un employé connecté
        $employee = \App\Models\User::where('role', 'employee')->first();
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $employee->id,
        ]);
        
        $employee->name = $request->name;
        $employee->email = $request->email;
        // Autres champs à mettre à jour
        
        $employee->save();
        
        return redirect()->route('employee.profile.index')
                         ->with('success', 'Profil mis à jour avec succès.');
    }
}
