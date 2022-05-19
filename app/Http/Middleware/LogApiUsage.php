<?php

namespace App\Http\Middleware;

use App\ApiUsage;
use Closure;

class LogApiUsage
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
        $res = $next($request);

        $usage = new ApiUsage();
        $usage->ip = $_SERVER['REMOTE_ADDR'];
        if (auth()->check()) {
            $usage->user_id = auth()->user()->id;
        }
        $usage->url = $_SERVER['REQUEST_URI'];
        $usage->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $usage->save();

        return $res;
    }
}
