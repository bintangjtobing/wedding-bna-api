<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class AdminAuthenticate extends Middleware
{
    protected function redirectTo(Request $request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
}
