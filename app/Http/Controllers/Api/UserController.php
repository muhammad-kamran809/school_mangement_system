<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => User::with([
                'teacher',
                'student',
                'staff',
                'studentParent',
                'roles'
            ])->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Admin,Teacher,Student,Parent,Staff',

            // Teacher / Student / Staff / Parent profile fields
            'phone' => 'nullable|string|max:30',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',

            // Teacher
            'qualification' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|numeric',

            // Staff
            'designation' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',

            // Student
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:50',
        ]);

        return DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Create Login User
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Assign Role
            |--------------------------------------------------------------------------
            */

            $user->assignRole($validated['role']);

            /*
            |--------------------------------------------------------------------------
            | Create Profile
            |--------------------------------------------------------------------------
            */

            if ($validated['role'] === 'Teacher') {

                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'qualification' => $validated['qualification'] ?? null,
                    'joining_date' => $validated['joining_date'] ?? null,
                    'salary' => $validated['salary'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                ]);

                return response()->json([
                    'message' => 'Teacher account and profile created successfully.',
                    'data' => [
                        'user' => $user,
                        'teacher' => $teacher,
                        'role' => 'Teacher',
                    ],
                ], 201);
            }

            if ($validated['role'] === 'Student') {

                $student = Student::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'guardian_name' => $validated['guardian_name'] ?? null,
                    'guardian_phone' => $validated['guardian_phone'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                ]);

                return response()->json([
                    'message' => 'Student account and profile created successfully.',
                    'data' => [
                        'user' => $user,
                        'student' => $student,
                        'role' => 'Student',
                    ],
                ], 201);
            }

            if ($validated['role'] === 'Staff') {

                $staff = Staff::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'designation' => $validated['designation'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'joining_date' => $validated['joining_date'] ?? null,
                    'salary' => $validated['salary'] ?? null,
                    'status' => $validated['status'] ?? 'active',
                ]);

                return response()->json([
                    'message' => 'Staff account and profile created successfully.',
                    'data' => [
                        'user' => $user,
                        'staff' => $staff,
                        'role' => 'Staff',
                    ],
                ], 201);
            }

            if ($validated['role'] === 'Parent') {

                $studentParent = StudentParent::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);

                return response()->json([
                    'message' => 'Parent account and profile created successfully.',
                    'data' => [
                        'user' => $user,
                        'parent' => $studentParent,
                        'role' => 'Parent',
                    ],
                ], 201);
            }

            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'message' => 'Admin account created successfully.',
                'data' => [
                    'user' => $user,
                    'role' => 'Admin',
                ],
            ], 201);
        });
    }

    public function show(User $user)
    {
        return response()->json([
            'data' => $user->load([
                'teacher',
                'student',
                'staff',
                'studentParent',
                'roles'
            ])
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return response()->json([
            'message' => 'User updated successfully.',
            'data' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.'
        ]);
    }
}
