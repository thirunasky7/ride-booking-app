<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Check Login
        |--------------------------------------------------------------------------
        */

        if (!auth()->check()) {

            return redirect()->route(
                'customer.login'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Check Customer Role
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->role != 'customer') {

            abort(403);
        }

        return $next($request);
    }
}