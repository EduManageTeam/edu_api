<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * GET /api/parents
     * Get all parents.
     */
    public function index()
    {
        $parents = ParentModel::all();

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $parents,
        ], 200);
    }

    /**
     * POST /api/parents
     * Create a new parent.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:2|max:100',
            'gender' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'photo' => 'nullable|string|max:255',
        ]);

        $parent = ParentModel::create($validated);

        return response()->json([
            'message' => 'Created Successfully',
            'status' => 201,
            'data' => $parent,
        ], 201);
    }

    /**
     * GET /api/parents/{id}
     * Get one parent by ID.
     */
    public function show(string $id)
    {
        $parent = ParentModel::find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $parent,
        ], 200);
    }

    /**
     * PUT /api/parents/{id}
     * Update a parent.
     */
    public function update(Request $request, string $id)
    {
        $parent = ParentModel::find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        $validated = $request->validate([
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'name' => 'sometimes|required|string|min:2|max:100',
            'gender' => 'sometimes|required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'photo' => 'nullable|string|max:255',
        ]);

        $parent->update($validated);

        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 200,
            'data' => $parent,
        ], 200);
    }

    /**
     * DELETE /api/parents/{id}
     * Delete a parent.
     */
    public function destroy(string $id)
    {
        $parent = ParentModel::find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        $parent->delete();

        return response()->json([
            'message' => 'Deleted Successfully',
            'status' => 200,
            'data' => null,
        ], 200);
    }
}
