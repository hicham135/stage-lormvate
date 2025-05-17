<?php
namespace App\Http\Controllers;
// app/Http/Controllers/TeamController.php

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;

class TeamController extends Controller
{
    public function index()
    {
        // Simulating a department head
        $departmentHead = User::where('role', 'department_head')->first();
        $departmentId = $departmentHead->department_id;
        
        $team = User::where('department_id', $departmentId)->get();
        
        return view('team.index', compact('team'));
    }
    
    public function show($id)
    {
        $member = User::findOrFail($id);
        return view('team.show', compact('member'));
    }
    
    public function edit($id)
    {
        $member = User::findOrFail($id);
        return view('team.edit', compact('member'));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);
        
        $member = User::findOrFail($id);
        $member->update($request->all());
        
        return redirect()->route('team.index')
                         ->with('success', 'Member updated successfully');
    }
}