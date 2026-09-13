<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index(): JsonResponse
    {
        $academicYears = AcademicYear::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $academicYears,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $academicYear = AcademicYear::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Academic year created successfully.',
            'data' => $academicYear,
        ], 201);
    }

    public function show(AcademicYear $academicYear): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $academicYear,
        ]);
    }

    public function update(
        Request $request,
        AcademicYear $academicYear
    ): JsonResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('academic_years', 'name')
                    ->ignore($academicYear->id),
            ],

            'start_date' => 'required|date',

            'end_date' => 'required|date|after:start_date',

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $academicYear->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Academic year updated successfully.',
            'data' => $academicYear->fresh(),
        ]);
    }

    public function destroy(AcademicYear $academicYear): JsonResponse
    {
        $academicYear->delete();

        return response()->json([
            'success' => true,
            'message' => 'Academic year deleted successfully.',
        ]);
    }
}