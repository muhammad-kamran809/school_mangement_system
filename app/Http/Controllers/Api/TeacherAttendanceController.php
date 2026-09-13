<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TeacherAttendance::with('teacher');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
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
            'teacher_id' => [
                'required',
                'exists:teachers,id',
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

        $alreadyExists = TeacherAttendance::where(
            'teacher_id',
            $validated['teacher_id']
        )
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Attendance for this teacher already exists for the selected date.'
            ], 422);
        }

        $attendance = TeacherAttendance::create($validated);

        return response()->json(
            $attendance->load('teacher'),
            201
        );
    }

    public function show(TeacherAttendance $teacherAttendance)
    {
        return response()->json(
            $teacherAttendance->load('teacher')
        );
    }

    public function update(
        Request $request,
        TeacherAttendance $teacherAttendance
    ) {
        $validated = $request->validate([
            'teacher_id' => [
                'required',
                'exists:teachers,id',
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

        $alreadyExists = TeacherAttendance::where(
            'teacher_id',
            $validated['teacher_id']
        )
            ->whereDate('date', $validated['date'])
            ->where('id', '!=', $teacherAttendance->id)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Attendance for this teacher already exists for the selected date.'
            ], 422);
        }

        $teacherAttendance->update($validated);

        return response()->json(
            $teacherAttendance->load('teacher')
        );
    }

    public function destroy(TeacherAttendance $teacherAttendance)
    {
        $teacherAttendance->delete();

        return response()->json([
            'message' => 'Teacher attendance deleted successfully.'
        ]);
    }
}
