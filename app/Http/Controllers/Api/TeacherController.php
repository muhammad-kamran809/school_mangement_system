<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    /**
     * Display a listing of teachers.
     */
    public function index(): JsonResponse
    {
        $teachers = Teacher::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    /**
     * Store a newly created teacher.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
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