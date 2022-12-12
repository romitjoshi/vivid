<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class RouteAuthourizationCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        //exit("RouteAuthourizationCustomer");
        if(Auth::user()->role != 2)
        {
            return redirect('login');
        }

        if(!empty(Auth::user()->status) && Auth::user()->status == 2)
        {
            //exit("hhh");
            Auth::logout();
            return redirect('/login');
        }
    
        return $next($request);
    }
}
