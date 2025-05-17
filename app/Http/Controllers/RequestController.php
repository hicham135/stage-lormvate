<?php
namespace App\Http\Controllers;
//app/Http/Controllers/RequestController.php

use Illuminate\Http\Request;
use App\Models\Request as DepartmentRequest;
use App\Models\User;

class RequestController extends Controller
{
    public function index()
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        $departmentId = $departmentHead->department_id;
        
        $requests = DepartmentRequest::where('department_id', $departmentId)
                                 ->with(['user', 'approver'])
                                 ->orderBy('created_at', 'desc')
                                 ->get();
        
        return view('requests.index', compact('requests'));
    }
    
    public function show($id)
    {
        $request = DepartmentRequest::with(['user', 'approver'])->findOrFail($id);
        return view('requests.show', compact('request'));
    }
    
    public function approve($id)
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        
        $request = DepartmentRequest::findOrFail($id);
        
        $request->update([
            'status' => 'approved',
            'approved_by' => $departmentHead->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('requests.index')
                         ->with('success', 'Request approved successfully');
    }
    
    public function reject($id)
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        
        $request = DepartmentRequest::findOrFail($id);
        
        $request->update([
            'status' => 'rejected',
            'approved_by' => $departmentHead->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('requests.index')
                         ->with('success', 'Request rejected successfully');
    }
}