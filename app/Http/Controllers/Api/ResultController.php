<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with([
            'exam',
            'student',
            'subject',
        ]);

        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        return response()->json(
            $query->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                'exists:exams,id',
            ],

            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'marks' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_marks' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'grade' => [
                'nullable',
                'string',
                'max:20',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        // Marks cannot be greater than total marks
        if ($validated['marks'] > $validated['total_marks']) {
            return response()->json([
                'message' => 'Marks obtained cannot be greater than total marks.'
            ], 422);
        }

        // Check if the result already exists
        $alreadyExists = Result::where(
            'exam_id',
            $validated['exam_id']
        )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'A result already exists for this student, exam, and subject.'
            ], 422);
        }

        // Make sure exam exists and get its class
        $exam = Exam::find($validated['exam_id']);

        // Check student enrollment in exam class
        $studentEnrolled = \App\Models\Enrollment::where(
            'student_id',
            $validated['student_id']
        )
            ->where(
                'class_id',
                $exam->class_id
            )
            ->where(
                'academic_year_id',
                $exam->academic_year_id
            )
            ->exists();

        if (!$studentEnrolled) {
            return response()->json([
                'message' => 'The selected student is not enrolled in the class and academic year of this exam.'
            ], 422);
        }

        $result = Result::create($validated);

        return response()->json(
            $result->load([
                'exam',
                'student',
                'subject',
            ]),
            201
        );
    }

    public function show(Result $result)
    {
        return response()->json(
            $result->load([
                'exam',
                'student',
                'subject',
            ])
        );
    }

    public function update(Request $request, Result $result)
    {
        $validated = $request->validate([
            'exam_id' => [
                'required',
                'exists:exams,id',
            ],

            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'marks' => [
                'required',
                'numeric',
                'min:0',
            ],

            'total_marks' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'grade' => [
                'nullable',
                'string',
                'max:20',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        if ($validated['marks'] > $validated['total_marks']) {
            return response()->json([
                'message' => 'Marks obtained cannot be greater than total marks.'
            ], 422);
        }

        $alreadyExists = Result::where(
            'exam_id',
            $validated['exam_id']
        )
            ->where(
                'student_id',
                $validated['student_id']
            )
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->where(
                'id',
                '!=',
                $result->id
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'A result already exists for this student, exam, and subject.'
            ], 422);
        }

        $exam = Exam::find($validated['exam_id']);

        $studentEnrolled = \App\Models\Enrollment::where(
            'student_id',
            $validated['student_id']
        )
            ->where(
                'class_id',
                $exam->class_id
            )
            ->where(
                'academic_year_id',
                $exam->academic_year_id
            )
            ->exists();

        if (!$studentEnrolled) {
            return response()->json([
                'message' => 'The selected student is not enrolled in the class and academic year of this exam.'
            ], 422);
        }

        $result->update($validated);

        return response()->json(
            $result->load([
                'exam',
                'student',
                'subject',
            ])
        );
    }

    public function destroy(Result $result)
    {
        $result->delete();

        return response()->json([
            'message' => 'Result deleted successfully.'
        ]);
    }
}
