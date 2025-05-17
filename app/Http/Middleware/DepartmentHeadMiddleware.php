<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentHeadMiddleware
{
<<<<<<< HEAD
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->role !== 'department_head') {
            return redirect()->route('login')
                ->with('error', 'Vous n\'êtes pas autorisé à accéder à cette section.');
        }
=======
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle(Request $request, Closure $next)
   {
       if (!Auth::check() || Auth::user()->role !== 'department_head') {
           return redirect()->route('login')
               ->with('error', 'Vous n\'êtes pas autorisé à accéder à cette section.');
       }
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00

       return $next($request);
   }
}