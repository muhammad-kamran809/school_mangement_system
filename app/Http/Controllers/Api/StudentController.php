<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Students retrieved successfully.'
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                'unique:students,user_id',
            ],

            'student_parents_id' => [
                'nullable',
                'integer',
                'exists:student_parents,id',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                'unique:students,email',
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'gender' => [
                'required',
                Rule::in(['male', 'female', 'other']),
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'guardian_name' => [
                'required',
                'string',
                'max:255',
            ],

            'guardian_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'photo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully.',
            'data' => $student,
        ], 201);
    }

    public function show(Student $student): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Student retrieved successfully.',
            'data' => $student,
        ]);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::unique('students', 'user_id')
                    ->ignore($student->id),
            ],

            'student_parents_id' => [
                'nullable',
                'integer',
                'exists:student_parents,id',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')
                    ->ignore($student->id),
            ],

            'phone' => ['nullable', 'string', 'max:30'],

            'gender' => [
                'required',
                Rule::in(['male', 'female', 'other']),
            ],

            'date_of_birth' => [
                'nullable',
                'date',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'guardian_name' => [
                'required',
                'string',
                'max:255',
            ],

            'guardian_phone' => [
                'required',
                'string',
                'max:30',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'photo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => $student->fresh(),
        ]);
    }

    public function destroy(Student $student): JsonResponse
    {
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully.',
        ]);
    }
}
