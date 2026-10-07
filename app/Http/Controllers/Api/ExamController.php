<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Exam::with([
            'academicYear',
            'schoolClass',
        ]);

        if ($request->filled('academic_year_id')) {
            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        if ($request->filled('class_id')) {
            $query->where(
                'class_id',
                $request->class_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('term', 'like', "%{$search}%");
            });
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Exams retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                'in:upcoming,ongoing,completed',
            ],
        ]);

        $alreadyExists = Exam::where(
            'name',
            $validated['name']
        )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'class_id',
                $validated['class_id']
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'This exam already exists for the selected academic year and class.',
            ], 422);
        }

        $exam = Exam::create($validated);

        return response()->json(
            $exam->load([
                'academicYear',
                'schoolClass',
            ]),
            201
        );
    }

    public function show(Exam $exam)
    {
        return response()->json(
            $exam->load([
                'academicYear',
                'schoolClass',
            ])
        );
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'start_date' => [
                'nullable',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'status' => [
                'required',
                'in:upcoming,ongoing,completed',
            ],
        ]);

        $alreadyExists = Exam::where(
            'name',
            $validated['name']
        )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'class_id',
                $validated['class_id']
            )
            ->where(
                'id',
                '!=',
                $exam->id
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'This exam already exists for the selected academic year and class.',
            ], 422);
        }

        $exam->update($validated);

        return response()->json(
            $exam->load([
                'academicYear',
                'schoolClass',
            ])
        );
    }

    public function destroy(Exam $exam)
    {
        if ($exam->results()->exists()) {
            return response()->json([
                'message' => 'Cannot delete this exam because results already exist for it.',
            ], 422);
        }

        $exam->delete();

        return response()->json([
            'message' => 'Exam deleted successfully.',
        ]);
    }
}
