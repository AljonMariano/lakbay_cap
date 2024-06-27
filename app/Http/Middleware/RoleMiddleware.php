<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        // Adjust the role check based on your needs
        if ($role === 'admin' && $user->isAdmin()) {
            return $next($request);
        } elseif ($role === 'user' && $user->isUser()) {
            return $next($request);
        }

        return redirect('admin/')->with('error', 'You do not have access to this section');
    }
}
