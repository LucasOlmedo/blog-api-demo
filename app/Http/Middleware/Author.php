<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Author
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
        if(Auth::guard()->user()->isAuthor() || Auth::guard()->user()->isAdmin()){
            return $next($request);
        }
        return response()->json([
            'error' => 'Unauthorized.'
        ], 401);
    }
}
