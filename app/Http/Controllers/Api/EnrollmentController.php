<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with([
            'student',
            'academicYear',
            'schoolClass',
            'section',
        ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Enrollments retrieved successfully.',
            'data' => $enrollments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'roll_no' => [
                'required',
                'string',
                'max:50',
            ],

            'enrollment_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $sectionBelongsToClass = \App\Models\Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        $alreadyEnrolled = Enrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'This student is already enrolled in the selected academic year.',
            ], 422);
        }

        $enrollment = Enrollment::create($validated);

        $enrollment->load([
            'student',
            'academicYear',
            'schoolClass',
            'section',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student enrolled successfully.',
            'data' => $enrollment,
        ], 201);
    }

    public function show(Enrollment $enrollment): JsonResponse
    {
        $enrollment->load([
            'student',
            'academicYear',
            'schoolClass',
            'section',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment retrieved successfully.',
            'data' => $enrollment,
        ]);
    }

    public function update(Request $request, Enrollment $enrollment): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'roll_no' => [
                'required',
                'string',
                'max:50',
            ],

            'enrollment_date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        $sectionBelongsToClass = \App\Models\Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        $alreadyEnrolled = Enrollment::where('student_id', $validated['student_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('id', '!=', $enrollment->id)
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'This student is already enrolled in the selected academic year.',
            ], 422);
        }

        $enrollment->update($validated);

        $enrollment->load([
            'student',
            'academicYear',
            'schoolClass',
            'section',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment updated successfully.',
            'data' => $enrollment,
        ]);
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment deleted successfully.',
        ]);
    }
}
