<?php

namespace App\Http\Middleware;

use Closure;

class CheckAdmin
{
    public function handle($request, Closure $next)
    {
        if (!session()->has('admin')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
