<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
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
        if (!auth()->check()) {
            return abort(401);
        }

        /** @var \App\User $user */
        $user = auth()->user();
        if (!$user->isAdmin()) {
            return abort(403);
        }

        return $next($request);
    }
}
