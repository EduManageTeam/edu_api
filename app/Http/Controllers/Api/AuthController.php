<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:150|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|max:255',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
            ],
        ]);

        // Get selected role
        $role = Role::findOrFail($validated['role_id']);
        $roleName = strtolower(trim($role->name));

        // Default passwords
        $defaultPasswords = [
            'teacher' => 'Teacher@123',
            'student' => 'Student@123',
            'parent'  => 'Parent@123',
        ];

        // Assign password based on role
        if (isset($defaultPasswords[$roleName])) {

            $password = $defaultPasswords[$roleName];

        } elseif ($roleName === 'admin') {

            if (empty($validated['password'])) {
                return response()->json([
                    'message' => 'Admin password is required'
                ], 422);
            }

            $password = $validated['password'];

        } else {

            return response()->json([
                'message' => 'Invalid role'
            ], 422);
        }

        // Auto generate unique User ID
        $validated['u_id'] = $this->generateUserId($roleName);

        // Hash password
        $validated['password'] = Hash::make($password);

        // Create user
        $user = User::create($validated);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user->load('role'),
        ], 201);
    }


    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'u_id' => 'required|string|max:20',
            'password' => 'required|string',
        ]);

        // Find user by u_id
        $user = User::where('u_id', $credentials['u_id'])->first();

        // Check password
        if (!$user || !Hash::check(
            $credentials['password'],
            $user->password
        )) {
            return response()->json([
                'message' => 'Invalid User ID or password',
            ], 401);
        }

        // Generate Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user->load('role'),
            'token' => $token,
        ], 200);
    }


    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }


    // AUTHENTICATED USER
    public function me(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json(
            $user->load('role')
        );
    }


    // GENERATE UNIQUE USER ID
    private function generateUserId(string $roleName): string
    {
        $prefix = match ($roleName) {
            'admin' => 'AD',
            'teacher' => 'TCH',
            'parent' => 'PAR',
            'student' => 'STU',
            default => throw new \InvalidArgumentException(
                'Invalid role'
            ),
        };

        do {
            $random = Str::upper(Str::random(6));
            $uId = $prefix . '-' . $random;

        } while (
            User::where('u_id', $uId)->exists()
        );

        return $uId;
    }
}
