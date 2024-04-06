<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebSecurity
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
        // dd("Hello World");
        if(session()->has('name'))
        {
            return $next($request);
        }else{
           return back()->with('warning', '"Please Login for access !"');
        }
    }
}
