<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MyParentController extends Controller
{
    /**
     * Get all children of logged-in parent
     */
    public function children(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        $query = Student::where(
            'student_parents_id',
            $user->studentParent->id
        )->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Children retrieved successfully.'
        );
    }

    /**
     * Get attendance of a specific child
     */
    public function attendance(Request $request, Student $student): JsonResponse
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

        $query = StudentAttendance::with([
            'schoolClass',
            'section',
        ])
            ->where('student_id', $student->id)
            ->latest('date');

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Child attendance retrieved successfully.'
        );
    }

    /**
     * Get results of a specific child
     */
    public function results(Request $request, Student $student): JsonResponse
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

        $query = Result::with([
            'exam',
            'subject',
        ])
            ->where('student_id', $student->id)
            ->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Child results retrieved successfully.'
        );
    }

    /**
     * Get fees of a specific child
     */
    public function fees(Request $request, Student $student): JsonResponse
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

        $query = Fee::with([
            'academicYear',
            'payments',
        ])
            ->where('student_id', $student->id)
            ->latest();

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Child fees retrieved successfully.'
        );
    }

    /**
     * Get payments of a specific child
     */
    public function payments(Request $request, Student $student): JsonResponse
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

        $query = Payment::with([
            'fee',
        ])
            ->where('student_id', $student->id)
            ->latest('payment_date');

        return $this->paginateResponse(
            $query,
            $request,
            10,
            'Child payments retrieved successfully.'
        );
    }
}
