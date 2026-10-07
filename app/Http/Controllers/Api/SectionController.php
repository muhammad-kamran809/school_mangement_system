<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Section::with('schoolClass');

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

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
            'Sections retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'name' => 'required|string|max:255',

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $sectionExists = Section::where('class_id', $validated['class_id'])
            ->where('name', $validated['name'])
            ->exists();

        if ($sectionExists) {
            return response()->json([
                'success' => false,
                'message' => 'This section already exists for the selected class.',
            ], 422);
        }

        $section = Section::create($validated);

        $section->load('schoolClass');

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully.',
            'data' => $section,
        ], 201);
    }

    public function show(Section $section): JsonResponse
    {
        $section->load('schoolClass');

        return response()->json([
            'success' => true,
            'data' => $section,
        ]);
    }

    public function update(
        Request $request,
        Section $section
    ): JsonResponse {
        $validated = $request->validate([
            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'name' => 'required|string|max:255',

            'capacity' => [
                'required',
                'integer',
                'min:1',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $sectionExists = Section::where('class_id', $validated['class_id'])
            ->where('name', $validated['name'])
            ->where('id', '!=', $section->id)
            ->exists();

        if ($sectionExists) {
            return response()->json([
                'success' => false,
                'message' => 'This section already exists for the selected class.',
            ], 422);
        }

        $section->update($validated);

        $section->load('schoolClass');

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully.',
            'data' => $section,
        ]);
    }

    public function destroy(Section $section): JsonResponse
    {
        if ($section->enrollments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this section because it has enrollments.',
            ], 422);
        }

        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully.',
        ]);
    }
}
