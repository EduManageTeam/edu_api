<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ParentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role->name !== 'parent') {
            return response()->json([
                'message' => 'Access Denied.',
            ], 403);
        }

        return $next($request);
    }
}
