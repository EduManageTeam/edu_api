<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role->name !== 'teacher') {
            return response()->json([
                'message' => 'Access Denied.',
            ], 403);
        }

        return $next($request);
    }
}
