<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;

class FeeReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::query()
            ->with([
                'student',
                'academicYear',
                'payments',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Academic Year Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Student Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('student_id')) {
            $query->where(
                'student_id',
                $request->student_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Fee Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('fee_type')) {
            $query->where(
                'fee_type',
                $request->fee_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'due_date',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {
            $query->whereDate(
                'due_date',
                '<=',
                $request->date_to
            );
        }

        $fees = $query
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Build Report
        |--------------------------------------------------------------------------
        */

        $report = $fees->map(function ($fee) {

            $totalFee = (float) $fee->amount;

            $totalPaid = (float) $fee->payments->sum(
                'amount'
            );

            $remaining = max(
                0,
                $totalFee - $totalPaid
            );

            /*
            |--------------------------------------------------------------------------
            | Calculate Status
            |--------------------------------------------------------------------------
            */

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

                'description' => $fee->description,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Payment Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $report = $report->filter(function ($item) use ($request) {
                return $item['status'] === $request->status;
            })->values();
        }

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalFees = $report->sum('total_fee');

        $totalPaid = $report->sum('paid');

        $totalRemaining = $report->sum('remaining');

        return response()->json([
            'message' => 'Fee report retrieved successfully.',

            'summary' => [
                'total_fee' => round($totalFees, 2),

                'total_paid' => round($totalPaid, 2),

                'total_remaining' => round($totalRemaining, 2),

                'total_records' => $report->count(),
            ],

            'data' => $report,
        ]);
    }
}
