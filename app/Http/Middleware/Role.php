<?php

namespace App\Http\Middleware;

use Closure;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if(!$user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'You do not have enough privilege to access this resource.'
            ], 403);
        }

        return $next($request);
    }
}
