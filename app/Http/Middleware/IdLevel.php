<?php

namespace App\Http\Middleware;

use Closure;

class IdLevel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->idlevel == 1){
            return $next($request);
        }elseif (auth()->user()->idlevel == 7) {
            return $next($request);
        }elseif (auth()->user()->idlevel == 8) {
            return $next($request);
        }elseif (auth()->user()->idlevel == 2) {
            return $next($request);
        } elseif (auth()->user()->idlevel == 4) {
            return $next($request);
        }
        return redirect(‘home’)->with(‘error’,"You don't have admin access.");
    }
}
