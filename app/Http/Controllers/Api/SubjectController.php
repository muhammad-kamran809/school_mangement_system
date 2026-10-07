<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Subjects retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:subjects,code',
            'description' => 'nullable|string',
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully.',
            'data' => $subject,
        ], 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Subject retrieved successfully.',
            'data' => $subject,
        ]);
    }

    public function update(
        Request $request,
        Subject $subject
    ): JsonResponse {
        $validated = $request->validate([
            'name' => 'required|string|max:255',

            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('subjects', 'code')
                    ->ignore($subject->id),
            ],

            'description' => 'nullable|string',

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully.',
            'data' => $subject->fresh(),
        ]);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        if ($subject->teacherAssignments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this subject because it has teacher assignments.',
            ], 422);
        }

        if ($subject->results()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this subject because it has results.',
            ], 422);
        }

        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully.',
        ]);
    }
}
