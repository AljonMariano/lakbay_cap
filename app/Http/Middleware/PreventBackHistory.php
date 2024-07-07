<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventBackHistory
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is an instance of Response or JsonResponse
        if ($response instanceof \Illuminate\Http\Response || $response instanceof \Illuminate\Http\JsonResponse) {
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
