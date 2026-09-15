<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class MyProfileController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Logged-in User Profile
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Profile retrieved successfully.',
            'data' => $user->load([
                'roles',
                'student',
                'teacher',
                'staff',
                'studentParent',
            ]),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Student - Own Profile
    |--------------------------------------------------------------------------
    */

    public function myStudent(Request $request)
    {
        $user = $request->user();

        if (! $user->student) {
            return response()->json([
                'message' => 'Student profile not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Student profile retrieved successfully.',
            'data' => $user->student,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Parent - Own Children
    |--------------------------------------------------------------------------
    */

    public function myChildren(Request $request)
    {
        $user = $request->user();

        if (! $user->studentParent) {
            return response()->json([
                'message' => 'Parent profile not found.',
            ], 404);
        }

        $children = Student::where(
            'student_parents_id',
            $user->studentParent->id
        )->get();

        return response()->json([
            'message' => 'Children retrieved successfully.',
            'data' => $children,
        ]);
    }
}
