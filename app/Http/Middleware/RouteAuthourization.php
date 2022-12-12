<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class RouteAuthourization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //exit("RouteAuthourization");
        if(!empty(Auth::user()->role) && Auth::user()->role == 1)
        {
            return $next($request);
        }        
        else
        {
            return redirect('/admin/login');
        }
       
    }
}
