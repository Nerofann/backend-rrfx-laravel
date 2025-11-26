<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jangan redirect untuk API request, return null saja
        if ($request->is('v1/*') || $request->expectsJson()) {
            return null;
        }
        
        // Untuk web request, redirect ke login (jika route login ada)
        return route('login');
    }
}
