<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceController extends Controller
{
    public function students(Request $request)
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $classId = $request->query('class_id');
        $sectionId = $request->query('section_id');

        if (! $classId || ! $sectionId) {
            return response()->json([
                'message' => 'class_id and section_id are required.',
            ], 422);
        }

        $isAssigned = $user->teacher
            ->assignments()
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->exists();

        if (! $isAssigned) {
            return response()->json([
                'message' => 'You are not assigned to this class and section.',
            ], 403);
        }

        $students = Student::whereHas('enrollments', function ($query) use ($classId, $sectionId) {
            $query->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->where('status', 'active');
        })->get();

        return response()->json([
            'message' => 'Students retrieved successfully.',
            'data' => $students,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $validated = $request->validate([
            'class_id' => 'required|integer|exists:classes,id',
            'section_id' => 'required|integer|exists:sections,id',
            'date' => 'required|date',
            'attendance' => 'required|array|min:1',
            'attendance.*.student_id' => 'required|integer|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,leave',
            'attendance.*.remarks' => 'nullable|string',
        ]);

        $isAssigned = $user->teacher
            ->assignments()
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->exists();

        if (! $isAssigned) {
            return response()->json([
                'message' => 'You are not assigned to this class and section.',
            ], 403);
        }

        foreach ($validated['attendance'] as $attendance) {
            $studentBelongsToClass = Student::where('id', $attendance['student_id'])
                ->whereHas('enrollments', function ($query) use ($validated) {
                    $query->where('class_id', $validated['class_id'])
                        ->where('section_id', $validated['section_id'])
                        ->where('status', 'active');
                })
                ->exists();

            if (! $studentBelongsToClass) {
                return response()->json([
                    'message' => 'One or more students do not belong to the selected class and section.',
                ], 403);
            }
        }

        DB::transaction(function () use ($validated) {
            foreach ($validated['attendance'] as $attendance) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $attendance['student_id'],
                        'class_id' => $validated['class_id'],
                        'section_id' => $validated['section_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $attendance['status'],
                        'remarks' => $attendance['remarks'] ?? null,
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Attendance saved successfully.',
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user->teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $query = StudentAttendance::with([
            'student',
            'schoolClass',
            'section',
        ]);

        $query->whereHas('schoolClass', function ($query) use ($user) {
            $query->whereIn(
                'id',
                $user->teacher
                    ->assignments()
                    ->pluck('class_id')
                    ->unique()
            );
        });

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        return response()->json([
            'message' => 'Attendance retrieved successfully.',
            'data' => $query->latest('date')->get(),
        ]);
    }
}
