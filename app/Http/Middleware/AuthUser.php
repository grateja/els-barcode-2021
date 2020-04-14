<?php

namespace App\Http\Middleware;

use Closure;

class AuthUser
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
        if($request->route('userId') != auth()->id()) {
            return response()->json([
                'status' => 'Fail',
                'message' => 'Unauthorized access!',
            ], 401);
        }

        return $next($request);
    }
}
