<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\TeacherAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TeacherAssignment::with([
            'teacher',
            'academicYear',
            'schoolClass',
            'section',
            'subject',
        ]);

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
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

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        $assignments = $query->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignments retrieved successfully.',
            'data' => $assignments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'exists:teachers,id',
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

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
        ]);

        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        $exists = TeacherAssignment::where([
            'teacher_id' => $validated['teacher_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'class_id' => $validated['class_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
        ])->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This teacher assignment already exists.',
            ], 422);
        }

        $assignment = TeacherAssignment::create($validated);

        $assignment->load([
            'teacher',
            'academicYear',
            'schoolClass',
            'section',
            'subject',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment created successfully.',
            'data' => $assignment,
        ], 201);
    }

    public function show(
        TeacherAssignment $teacherAssignment
    ): JsonResponse {
        $teacherAssignment->load([
            'teacher',
            'academicYear',
            'schoolClass',
            'section',
            'subject',
        ]);

        return response()->json([
            'success' => true,
            'data' => $teacherAssignment,
        ]);
    }

    public function update(
        Request $request,
        TeacherAssignment $teacherAssignment
    ): JsonResponse {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'exists:teachers,id',
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

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
        ]);

        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'success' => false,
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        $exists = TeacherAssignment::where([
            'teacher_id' => $validated['teacher_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'class_id' => $validated['class_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
        ])
            ->where('id', '!=', $teacherAssignment->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This teacher assignment already exists.',
            ], 422);
        }

        $teacherAssignment->update($validated);

        $teacherAssignment->load([
            'teacher',
            'academicYear',
            'schoolClass',
            'section',
            'subject',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment updated successfully.',
            'data' => $teacherAssignment,
        ]);
    }

    public function destroy(
        TeacherAssignment $teacherAssignment
    ): JsonResponse {
        $teacherAssignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher assignment deleted successfully.',
        ]);
    }
}