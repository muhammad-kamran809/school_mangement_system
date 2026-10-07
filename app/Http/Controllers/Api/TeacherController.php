<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Teacher::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Teachers retrieved successfully.'
        );
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                'unique:teachers,user_id',
            ],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher created successfully.',
            'data' => $teacher,
        ], 201);
    }

    /**
     * Display the specified teacher.
     */
    public function show(Teacher $teacher): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    /**
     * Update the specified teacher.
     */
    public function update(Request $request, Teacher $teacher): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                Rule::unique('teachers', 'user_id')
                    ->ignore($teacher->id),
            ],
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'photo' => 'nullable|string|max:255',
        ]);

        $teacher->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully.',
            'data' => $teacher,
        ]);
    }

    /**
     * Remove the specified teacher.
     */
    public function destroy(Teacher $teacher): JsonResponse
    {
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully.',
        ]);
    }
}
