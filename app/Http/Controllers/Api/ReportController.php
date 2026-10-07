<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Result;
use App\Models\Student;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Student Report
    |--------------------------------------------------------------------------
    */

    public function students(Request $request)
    {
        $query = Student::query()
            ->with([
                'enrollments.academicYear',
                'enrollments.schoolClass',
                'enrollments.section',
            ]);

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    '%'.$search.'%'
                )
                    ->orWhere(
                        'email',
                        'like',
                        '%'.$search.'%'
                    )
                    ->orWhere(
                        'phone',
                        'like',
                        '%'.$search.'%'
                    )
                    ->orWhere(
                        'guardian_name',
                        'like',
                        '%'.$search.'%'
                    );
            });
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Student report retrieved successfully.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Attendance Report
    |--------------------------------------------------------------------------
    */

    public function attendance(Request $request)
    {
        $query = StudentAttendance::query()
            ->with([
                'student',
                'schoolClass',
                'section',
            ]);

        if ($request->filled('student_id')) {
            $query->where(
                'student_id',
                $request->student_id
            );
        }

        if ($request->filled('class_id')) {
            $query->where(
                'class_id',
                $request->class_id
            );
        }

        if ($request->filled('section_id')) {
            $query->where(
                'section_id',
                $request->section_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'date',
                '<=',
                $request->date_to
            );
        }

        $summary = [
            'total_records' => (clone $query)->count(),

            'present' => (clone $query)
                ->where('status', 'present')
                ->count(),

            'absent' => (clone $query)
                ->where('status', 'absent')
                ->count(),

            'late' => (clone $query)
                ->where('status', 'late')
                ->count(),

            'leave' => (clone $query)
                ->where('status', 'leave')
                ->count(),
        ];

        return $this->paginateResponse(
            $query->latest('date'),
            $request,
            10,
            'Attendance report retrieved successfully.',
            ['summary' => $summary]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fee Report
    |--------------------------------------------------------------------------
    */

    public function fees(Request $request)
    {
        $query = Fee::query()
            ->with([
                'student',
                'academicYear',
                'payments',
            ]);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('student_id')) {
            $query->where(
                'student_id',
                $request->student_id
            );
        }

        if ($request->filled('fee_type')) {
            $query->where(
                'fee_type',
                $request->fee_type
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'due_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'due_date',
                '<=',
                $request->date_to
            );
        }

        $fees = $query
            ->latest('due_date')
            ->get();

        $report = $fees->map(function ($fee) {

            $totalFee = (float) $fee->amount;

            $totalPaid = (float) $fee->payments->sum(
                'amount'
            );

            $remaining = max(
                0,
                $totalFee - $totalPaid
            );

            if ($totalPaid >= $totalFee) {

                $status = 'paid';
            } elseif ($totalPaid > 0) {

                $status = 'partial';
            } elseif (
                $fee->due_date &&
                $fee->due_date->isPast()
            ) {

                $status = 'overdue';
            } else {

                $status = 'pending';
            }

            return [
                'fee_id' => $fee->id,

                'student_id' => $fee->student_id,

                'student_name' => $fee->student?->name,

                'academic_year_id' => $fee->academic_year_id,

                'academic_year' => $fee->academicYear?->name,

                'fee_type' => $fee->fee_type,

                'total_fee' => $totalFee,

                'paid' => $totalPaid,

                'remaining' => $remaining,

                'due_date' => $fee->due_date?->format('Y-m-d'),

                'status' => $status,
            ];
        });

        if ($request->filled('status')) {
            $report = $report
                ->filter(function ($item) use ($request) {
                    return $item['status'] === $request->status;
                })
                ->values();
        }

        $summary = [
            'total_fee' => round(
                $report->sum('total_fee'),
                2
            ),

            'total_paid' => round(
                $report->sum('paid'),
                2
            ),

            'total_remaining' => round(
                $report->sum('remaining'),
                2
            ),

            'total_records' => $report->count(),
        ];

        return $this->paginateResponse(
            $report,
            $request,
            10,
            'Fee report retrieved successfully.',
            ['summary' => $summary]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Result Report
    |--------------------------------------------------------------------------
    */

    public function results(Request $request)
    {
        $query = Result::query()
            ->with([
                'exam',
                'student',
                'subject',
            ]);

        if ($request->filled('exam_id')) {
            $query->where(
                'exam_id',
                $request->exam_id
            );
        }

        if ($request->filled('student_id')) {
            $query->where(
                'student_id',
                $request->student_id
            );
        }

        if ($request->filled('subject_id')) {
            $query->where(
                'subject_id',
                $request->subject_id
            );
        }

        $totalMarks = (float) (clone $query)->sum('total_marks');
        $obtainedMarks = (float) (clone $query)->sum('marks');
        $percentage = $totalMarks > 0
            ? round(($obtainedMarks / $totalMarks) * 100, 2)
            : 0;

        $summary = [
            'total_subjects' => (clone $query)->count(),
            'total_marks' => $totalMarks,
            'obtained_marks' => $obtainedMarks,
            'percentage' => $percentage,
        ];

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Result report retrieved successfully.',
            ['summary' => $summary]
        );
    }
}
