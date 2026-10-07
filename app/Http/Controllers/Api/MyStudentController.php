<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyStudentController extends Controller
{
    public function attendance(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $query = StudentAttendance::with([
            'schoolClass',
            'section',
        ])
            ->where('student_id', $user->student->id)
            ->latest('date');

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Attendance retrieved successfully.'
        );
    }

    public function results(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $query = Result::with([
            'exam',
            'subject',
        ])
            ->where('student_id', $user->student->id)
            ->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Results retrieved successfully.'
        );
    }

    public function fees(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $query = Fee::where('student_id', $user->student->id)
            ->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Fees retrieved successfully.'
        );
    }

    public function payments(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        $query = Payment::where('student_id', $user->student->id)
            ->latest('payment_date');

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Payments retrieved successfully.'
        );
    }
}
