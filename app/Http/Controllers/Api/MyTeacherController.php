<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyTeacherController extends Controller
{
    public function assignments(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $query = $user->teacher
            ->assignments()
            ->with([
                'academicYear',
                'schoolClass',
                'section',
                'subject',
            ])
            ->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Teacher assignments retrieved successfully.'
        );
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

        if ($assignments->isEmpty()) {
            return $this->paginateResponse(
                collect(),
                $request,
                10,
                'Assigned students retrieved successfully.'
            );
        }

        $query = Student::whereHas('enrollments', function ($query) use ($assignments) {
            $query->where(function ($q) use ($assignments) {
                foreach ($assignments as $assignment) {
                    $q->orWhere(function ($subQuery) use ($assignment) {
                        $subQuery
                            ->where('class_id', $assignment->class_id)
                            ->where('section_id', $assignment->section_id);
                    });
                }
            });
        })->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Assigned students retrieved successfully.'
        );
    }
}
