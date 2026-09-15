<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class MyParentController extends Controller
{
    /**
     * Get all children of logged-in parent
     */
    public function children(Request $request)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        $children = Student::where(
            'student_parents_id',
            $user->studentParent->id
        )->get();

        return response()->json([
            'message' => 'Children retrieved successfully.',
            'data' => $children,
        ]);
    }

    /**
     * Get attendance of a specific child
     */
    public function attendance(Request $request, Student $student)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        if ($student->student_parents_id !== $user->studentParent->id) {
            return response()->json([
                'message' => 'You are not allowed to access this student.',
            ], 403);
        }

        $attendance = StudentAttendance::with([
            'schoolClass',
            'section',
        ])
            ->where('student_id', $student->id)
            ->latest('date')
            ->get();

        return response()->json([
            'message' => 'Child attendance retrieved successfully.',
            'data' => $attendance,
        ]);
    }

    /**
     * Get results of a specific child
     */
    public function results(Request $request, Student $student)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        if ($student->student_parents_id !== $user->studentParent->id) {
            return response()->json([
                'message' => 'You are not allowed to access this student.',
            ], 403);
        }

        $results = Result::with([
            'exam',
            'subject',
        ])
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'message' => 'Child results retrieved successfully.',
            'data' => $results,
        ]);
    }

    /**
     * Get fees of a specific child
     */
    public function fees(Request $request, Student $student)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        if ($student->student_parents_id !== $user->studentParent->id) {
            return response()->json([
                'message' => 'You are not allowed to access this student.',
            ], 403);
        }

        $fees = Fee::with([
            'academicYear',
            'payments',
        ])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Child fees retrieved successfully.',
            'data' => $fees,
        ]);
    }

    /**
     * Get payments of a specific child
     */
    public function payments(Request $request, Student $student)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        if ($student->student_parents_id !== $user->studentParent->id) {
            return response()->json([
                'message' => 'You are not allowed to access this student.',
            ], 403);
        }

        $payments = Payment::with([
            'fee',
        ])
            ->where('student_id', $student->id)
            ->latest('payment_date')
            ->get();

        return response()->json([
            'message' => 'Child payments retrieved successfully.',
            'data' => $payments,
        ]);
    }
}
