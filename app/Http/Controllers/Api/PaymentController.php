<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fee;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with([
            'fee',
            'student',
        ]);

        if ($request->filled('fee_id')) {
            $query->where('fee_id', $request->fee_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('payment_method')) {
            $query->where(
                'payment_method',
                $request->payment_method
            );
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'payment_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'payment_date',
                '<=',
                $request->date_to
            );
        }

        return $this->paginateResponse(
            $query->latest('payment_date'),
            $request,
            10,
            'Payments retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fee_id' => [
                'required',
                'exists:fees,id',
            ],

            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_date' => [
                'required',
                'date',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'online',
                    'cheque',
                ]),
            ],

            'receipt_number' => [
                'required',
                'string',
                'max:255',
                'unique:payments,receipt_number',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Check that the selected student owns the selected fee
        |--------------------------------------------------------------------------
        */

        $fee = Fee::find($validated['fee_id']);

        if ((int) $fee->student_id !== (int) $validated['student_id']) {
            return response()->json([
                'message' => 'The selected fee does not belong to the selected student.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Check remaining amount
        |--------------------------------------------------------------------------
        */

        $alreadyPaid = Payment::where(
            'fee_id',
            $validated['fee_id']
        )->sum('amount');

        $remainingAmount = $fee->amount - $alreadyPaid;

        if ($validated['amount'] > $remainingAmount) {
            return response()->json([
                'message' => 'Payment amount cannot be greater than the remaining fee amount.',
                'fee_amount' => (float) $fee->amount,
                'already_paid' => (float) $alreadyPaid,
                'remaining_amount' => (float) $remainingAmount,
            ], 422);
        }

        $payment = Payment::create($validated);

        /*
        |--------------------------------------------------------------------------
        | Update fee status automatically
        |--------------------------------------------------------------------------
        */

        $totalPaid = Payment::where(
            'fee_id',
            $fee->id
        )->sum('amount');

        if ($totalPaid >= $fee->amount) {
            $fee->update([
                'status' => 'paid',
            ]);
        } elseif ($totalPaid > 0) {
            $fee->update([
                'status' => 'partial',
            ]);
        }

        return response()->json([
            'message' => 'Payment created successfully.',
            'data' => $payment->load([
                'fee',
                'student',
            ]),
        ], 201);
    }

    public function show(Payment $payment)
    {
        return response()->json([
            'message' => 'Payment retrieved successfully.',
            'data' => $payment->load([
                'fee',
                'student',
            ]),
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'fee_id' => [
                'sometimes',
                'required',
                'exists:fees,id',
            ],

            'student_id' => [
                'sometimes',
                'required',
                'exists:students,id',
            ],

            'amount' => [
                'sometimes',
                'required',
                'numeric',
                'min:0.01',
            ],

            'payment_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'payment_method' => [
                'sometimes',
                'required',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'online',
                    'cheque',
                ]),
            ],

            'receipt_number' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('payments', 'receipt_number')
                    ->ignore($payment->id),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $feeId = $validated['fee_id'] ?? $payment->fee_id;
        $studentId = $validated['student_id'] ?? $payment->student_id;
        $amount = $validated['amount'] ?? $payment->amount;

        $fee = Fee::find($feeId);

        /*
        |--------------------------------------------------------------------------
        | Check fee belongs to student
        |--------------------------------------------------------------------------
        */

        if ((int) $fee->student_id !== (int) $studentId) {
            return response()->json([
                'message' => 'The selected fee does not belong to the selected student.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate remaining amount excluding current payment
        |--------------------------------------------------------------------------
        */

        $alreadyPaid = Payment::where(
            'fee_id',
            $fee->id
        )
            ->where(
                'id',
                '!=',
                $payment->id
            )
            ->sum('amount');

        $remainingAmount = $fee->amount - $alreadyPaid;

        if ($amount > $remainingAmount) {
            return response()->json([
                'message' => 'Payment amount cannot be greater than the remaining fee amount.',
                'fee_amount' => (float) $fee->amount,
                'already_paid' => (float) $alreadyPaid,
                'remaining_amount' => (float) $remainingAmount,
            ], 422);
        }

        $payment->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Recalculate fee status
        |--------------------------------------------------------------------------
        */

        $totalPaid = Payment::where(
            'fee_id',
            $fee->id
        )->sum('amount');

        if ($totalPaid >= $fee->amount) {
            $fee->update([
                'status' => 'paid',
            ]);
        } elseif ($totalPaid > 0) {
            $fee->update([
                'status' => 'partial',
            ]);
        } else {
            $fee->update([
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Payment updated successfully.',
            'data' => $payment->load([
                'fee',
                'student',
            ]),
        ]);
    }

    public function destroy(Payment $payment)
    {
        $fee = $payment->fee;

        $payment->delete();

        /*
        |--------------------------------------------------------------------------
        | Recalculate fee status after deleting payment
        |--------------------------------------------------------------------------
        */

        $totalPaid = Payment::where(
            'fee_id',
            $fee->id
        )->sum('amount');

        if ($totalPaid >= $fee->amount) {
            $fee->update([
                'status' => 'paid',
            ]);
        } elseif ($totalPaid > 0) {
            $fee->update([
                'status' => 'partial',
            ]);
        } else {
            $fee->update([
                'status' => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Payment deleted successfully.',
        ]);
    }
}
