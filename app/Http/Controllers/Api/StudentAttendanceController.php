<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentAttendance::with([
            'student',
            'schoolClass',
            'section',
        ]);

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json(
            $query->latest('date')->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:present,absent,late,leave',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        // Make sure section belongs to selected class
        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (!$sectionBelongsToClass) {
            return response()->json([
                'message' => 'The selected section does not belong to the selected class.'
            ], 422);
        }

        // Make sure student is enrolled in the selected class and section
        $studentBelongsToClassSection = \App\Models\Enrollment::where(
            'student_id',
            $validated['student_id']
        )
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->exists();

        if (!$studentBelongsToClassSection) {
            return response()->json([
                'message' => 'The selected student is not enrolled in the selected class and section.'
            ], 422);
        }

        // Prevent duplicate attendance
        $alreadyExists = StudentAttendance::where(
            'student_id',
            $validated['student_id']
        )
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Attendance for this student already exists for the selected date.'
            ], 422);
        }

        $attendance = StudentAttendance::create($validated);

        return response()->json(
            $attendance->load([
                'student',
                'schoolClass',
                'section',
            ]),
            201
        );
    }

    public function show(StudentAttendance $studentAttendance)
    {
        return response()->json(
            $studentAttendance->load([
                'student',
                'schoolClass',
                'section',
            ])
        );
    }

    public function update(
        Request $request,
        StudentAttendance $studentAttendance
    ) {
        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'date' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                'in:present,absent,late,leave',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        // Make sure section belongs to selected class
        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (!$sectionBelongsToClass) {
            return response()->json([
                'message' => 'The selected section does not belong to the selected class.'
            ], 422);
        }

        // Make sure student belongs to selected class and section
        $studentBelongsToClassSection = \App\Models\Enrollment::where(
            'student_id',
            $validated['student_id']
        )
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->exists();

        if (!$studentBelongsToClassSection) {
            return response()->json([
                'message' => 'The selected student is not enrolled in the selected class and section.'
            ], 422);
        }

        // Check duplicate attendance except current record
        $alreadyExists = StudentAttendance::where(
            'student_id',
            $validated['student_id']
        )
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $studentAttendance->id)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Attendance for this student already exists for the selected date.'
            ], 422);
        }

        $studentAttendance->update($validated);

        return response()->json(
            $studentAttendance->load([
                'student',
                'schoolClass',
                'section',
            ])
        );
    }

    public function destroy(StudentAttendance $studentAttendance)
    {
        $studentAttendance->delete();

        return response()->json([
            'message' => 'Student attendance deleted successfully.'
        ]);
    }
}
