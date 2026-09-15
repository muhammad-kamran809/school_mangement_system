<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class MyStudentController extends Controller
{
    public function attendance(Request $request)
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $attendance = StudentAttendance::with([
            'schoolClass',
            'section',
        ])
            ->where('student_id', $user->student->id)
            ->latest('date')
            ->get();

        return response()->json([
            'message' => 'Attendance retrieved successfully.',
            'data' => $attendance,
        ]);
    }

    public function results(Request $request)
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $results = Result::with([
            'exam',
            'subject',
        ])
            ->where('student_id', $user->student->id)
            ->get();

        return response()->json([
            'message' => 'Results retrieved successfully.',
            'data' => $results,
        ]);
    }

    public function fees(Request $request)
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $fees = Fee::where('student_id', $user->student->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Fees retrieved successfully.',
            'data' => $fees,
        ]);
    }

    public function payments(Request $request)
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $payments = Payment::where('student_id', $user->student->id)
            ->latest('payment_date')
            ->get();

        return response()->json([
            'message' => 'Payments retrieved successfully.',
            'data' => $payments,
        ]);
    }
}
