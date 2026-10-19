<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrTeacher
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = $request->user();

        $role = strtolower(
            trim($user?->role?->name ?? '')
        );

        if (!in_array($role, ['admin', 'teacher'])) {
            return response()->json([
                'message' => 'Access Denied'
            ], 403);
        }

        return $next($request);
    }
}
