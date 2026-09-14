<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function connectTeacher(Request $request, User $user)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($user->teacher) {
            return response()->json([
                'message' => 'This user is already connected to a teacher.',
            ], 422);
        }

        $teacher = Teacher::findOrFail($validated['teacher_id']);

        if ($teacher->user_id) {
            return response()->json([
                'message' => 'This teacher is already connected to another user.',
            ], 422);
        }

        $teacher->user_id = $user->id;
        $teacher->save();

        return response()->json([
            'message' => 'Teacher account connected successfully.',
            'data' => $user->load('teacher'),
        ]);
    }

    public function connectStudent(Request $request, User $user)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        if ($user->student) {
            return response()->json([
                'message' => 'This user is already connected to a student.',
            ], 422);
        }

        $student = Student::findOrFail($validated['student_id']);

        if ($student->user_id) {
            return response()->json([
                'message' => 'This student is already connected to another user.',
            ], 422);
        }

        $student->user_id = $user->id;
        $student->save();

        return response()->json([
            'message' => 'Student account connected successfully.',
            'data' => $user->load('student'),
        ]);
    }

    public function connectStaff(Request $request, User $user)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
        ]);

        if ($user->staff) {
            return response()->json([
                'message' => 'This user is already connected to staff.',
            ], 422);
        }

        $staff = Staff::findOrFail($validated['staff_id']);

        if ($staff->user_id) {
            return response()->json([
                'message' => 'This staff member is already connected to another user.',
            ], 422);
        }

        $staff->user_id = $user->id;
        $staff->save();

        return response()->json([
            'message' => 'Staff account connected successfully.',
            'data' => $user->load('staff'),
        ]);
    }
}
