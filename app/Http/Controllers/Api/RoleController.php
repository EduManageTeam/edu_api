<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\RoleResourceCollection;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return new RoleResourceCollection($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:70|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::create($validated);
        return (new RoleResource($role))->response()->setStatusCode(201);
    }

    public function show(string $id)
    {
        $role = Role::with('users')->findOrFail($id);
        return new RoleResource($role);
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:70', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => 'nullable|string|max:255',
        ]);

        $role->update($validated);
        return new RoleResource($role);
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(null, 204);
    }
}
