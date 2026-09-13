<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fee::with([
            'student',
            'academicYear'
        ]);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('fee_type')) {
            $query->where('fee_type', $request->fee_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $fees = $query
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Fees retrieved successfully.',
            'data' => $fees
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id'
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id'
            ],

            'fee_type' => [
                'required',
                'string',
                'max:255'
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0'
            ],

            'due_date' => [
                'required',
                'date'
            ],

            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'paid',
                    'partial',
                    'overdue'
                ])
            ],

            'description' => [
                'nullable',
                'string'
            ],
        ]);

        $fee = Fee::create($validated);

        return response()->json([
            'message' => 'Fee created successfully.',
            'data' => $fee->load([
                'student',
                'academicYear'
            ])
        ], 201);
    }

    public function show(Fee $fee)
    {
        $fee->load([
            'student',
            'academicYear',
            'payments'
        ]);

        return response()->json([
            'message' => 'Fee retrieved successfully.',
            'data' => $fee
        ]);
    }

    public function update(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'student_id' => [
                'sometimes',
                'required',
                'exists:students,id'
            ],

            'academic_year_id' => [
                'sometimes',
                'required',
                'exists:academic_years,id'
            ],

            'fee_type' => [
                'sometimes',
                'required',
                'string',
                'max:255'
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric',
                'min:0'
            ],

            'due_date' => [
                'sometimes',
                'required',
                'date'
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'pending',
                    'paid',
                    'partial',
                    'overdue'
                ])
            ],

            'description' => [
                'nullable',
                'string'
            ],
        ]);

        $fee->update($validated);

        return response()->json([
            'message' => 'Fee updated successfully.',
            'data' => $fee->load([
                'student',
                'academicYear'
            ])
        ]);
    }

    public function destroy(Fee $fee)
    {
        if ($fee->payments()->exists()) {
            return response()->json([
                'message' => 'This fee cannot be deleted because payments already exist for it.'
            ], 422);
        }

        $fee->delete();

        return response()->json([
            'message' => 'Fee deleted successfully.'
        ]);
    }
}
