<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectResourceCollection;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::paginate();
        return new SubjectResourceCollection($subjects);
    }

    public function store(StoreSubjectRequest $request)
    {
        $subject = Subject::create($request->validated());
        return (new SubjectResource($subject))->response()->setStatusCode(201);
    }

    public function show(string $id)
    {
        $subject = Subject::findOrFail($id);
        return new SubjectResource($subject);
    }

    public function update(UpdateSubjectRequest $request, string $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update($request->validated());
        return new SubjectResource($subject);
    }

    public function destroy(string $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return response()->json(null, 204);
    }
}
