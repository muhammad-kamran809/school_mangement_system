<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyTeacherController extends Controller
{
    public function assignments(Request $request)
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $assignments = $user->teacher
            ->assignments()
            ->with([
                'academicYear',
                'schoolClass',
                'section',
                'subject',
            ])
            ->get();

        return response()->json([
            'message' => 'Teacher assignments retrieved successfully.',
            'data' => $assignments,
        ]);
    }

    public function students(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $assignments = $user->teacher
            ->assignments()
            ->get([
                'class_id',
                'section_id',
            ]);

        $students = $assignments->isEmpty()
            ? collect()
            : Student::whereHas('enrollments', function ($query) use ($assignments) {
                $query->where(function ($q) use ($assignments) {
                    foreach ($assignments as $assignment) {
                        $q->orWhere(function ($subQuery) use ($assignment) {
                            $subQuery
                                ->where('class_id', $assignment->class_id)
                                ->where('section_id', $assignment->section_id);
                        });
                    }
                });
            })->get();

        return response()->json([
            'message' => 'Assigned students retrieved successfully.',
            'data' => $students,
        ]);
    }
}
