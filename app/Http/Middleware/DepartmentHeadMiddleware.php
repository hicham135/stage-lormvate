<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentHeadMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'department_head') {
            return redirect()->route('dashboard')
                ->with('error', 'Vous n\'êtes pas autorisé à accéder à cette section.');
        }

        return $next($request);
    }
}