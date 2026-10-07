<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SchoolClass::with('sections');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Classes retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name',
            'description' => 'nullable|string',
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $schoolClass = SchoolClass::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class created successfully.',
            'data' => $schoolClass,
        ], 201);
    }

    public function show(SchoolClass $schoolClass): JsonResponse
    {
        $schoolClass->load('sections');

        return response()->json([
            'success' => true,
            'data' => $schoolClass,
        ]);
    }

    public function update(
        Request $request,
        SchoolClass $schoolClass
    ): JsonResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')
                    ->ignore($schoolClass->id),
            ],

            'description' => 'nullable|string',

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $schoolClass->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class updated successfully.',
            'data' => $schoolClass->fresh(),
        ]);
    }

    public function destroy(SchoolClass $schoolClass): JsonResponse
    {
        $hasRelatedRecords = $schoolClass->sections()->exists()
            || $schoolClass->enrollments()->exists()
            || $schoolClass->teacherAssignments()->exists()
            || $schoolClass->timetables()->exists()
            || $schoolClass->studentAttendances()->exists()
            || $schoolClass->exams()->exists();

        if ($hasRelatedRecords) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this class because it has related records.',
            ], 422);
        }

        if (! $schoolClass->delete()) {
            return response()->json([
                'success' => false,
                'message' => 'The class could not be deleted.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully.',
        ]);
    }
}
