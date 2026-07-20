<?php

namespace App\Http\Middleware;

use App\Models\Driver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiDriver
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user instanceof Driver) {
            return response()->json([
                'status' => false,
                'message' => 'Driver authentication required.',
            ], 403);
        }

        return $next($request);
    }
}
