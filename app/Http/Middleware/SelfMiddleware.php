<?php

namespace App\Http\Middleware;

use Closure;

class SelfMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $key)
    {
        if($request->route($key) != auth()->id() && $request->route($key) != 'self') {
            return response()->json([
                'message' => 'You do not have permission to access this resource.'
            ], 403);
        }
        return $next($request);
    }
}
