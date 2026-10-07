<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassRoomRequest;
use App\Http\Requests\UpdateClassRoomRequest;
use App\Http\Resources\ClassRoomResource;
use App\Http\Resources\ClassRoomResourceCollection;
use App\Models\ClassRoom;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassRoom::with('teacher')->paginate();
        return new ClassRoomResourceCollection($classes);
    }

    public function store(StoreClassRoomRequest $request)
    {
        $class = ClassRoom::create($request->validated());
        return (new ClassRoomResource($class->load('teacher')))->response()->setStatusCode(201);
    }

    public function show(string $id)
    {
        $class = ClassRoom::with('teacher')->findOrFail($id);
        return new ClassRoomResource($class);
    }

    public function update(UpdateClassRoomRequest $request, string $id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->update($request->validated());
        return new ClassRoomResource($class->load('teacher'));
    }

    public function destroy(string $id)
    {
        $class = ClassRoom::findOrFail($id);
        $class->delete();
        return response()->json(null, 204);
    }
}
