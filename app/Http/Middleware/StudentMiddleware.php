<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $roleName = strtolower(
            trim($user->role?->name ?? '')
        );

        if ($roleName !== 'student') {
            return response()->json([
                'message' => 'Access denied. Student only.'
            ], 403);
        }

        return $next($request);
    }
}
