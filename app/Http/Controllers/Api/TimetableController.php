<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Timetable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Timetable::with([
            'academicYear',
            'schoolClass',
            'section',
            'subject',
            'teacher',
        ]);

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        return $this->paginateResponse(
            $query->latest(),
            $request,
            10,
            'Timetables retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'teacher_id' => [
                'required',
                'exists:teachers,id',
            ],

            'day' => [
                'required',
                'string',
                'max:20',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'room' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        // Make sure section belongs to selected class
        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        // Prevent duplicate timetable slot
        $duplicate = Timetable::where('academic_year_id', $validated['academic_year_id'])
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('day', $validated['day'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'This timetable slot already exists for the selected class and section.',
            ], 422);
        }

        $timetable = Timetable::create($validated);

        return response()->json(
            $timetable->load([
                'academicYear',
                'schoolClass',
                'section',
                'subject',
                'teacher',
            ]),
            201
        );
    }

    public function show(Timetable $timetable)
    {
        return response()->json(
            $timetable->load([
                'academicYear',
                'schoolClass',
                'section',
                'subject',
                'teacher',
            ])
        );
    }

    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'class_id' => [
                'required',
                'exists:classes,id',
            ],

            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],

            'teacher_id' => [
                'required',
                'exists:teachers,id',
            ],

            'day' => [
                'required',
                'string',
                'max:20',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'room' => [
                'nullable',
                'string',
                'max:100',
            ],
        ]);

        // Make sure section belongs to selected class
        $sectionBelongsToClass = Section::where('id', $validated['section_id'])
            ->where('class_id', $validated['class_id'])
            ->exists();

        if (! $sectionBelongsToClass) {
            return response()->json([
                'message' => 'The selected section does not belong to the selected class.',
            ], 422);
        }

        // Check duplicate slot except current timetable
        $duplicate = Timetable::where('academic_year_id', $validated['academic_year_id'])
            ->where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('day', $validated['day'])
            ->where('start_time', $validated['start_time'])
            ->where('id', '!=', $timetable->id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'This timetable slot already exists for the selected class and section.',
            ], 422);
        }

        $timetable->update($validated);

        return response()->json(
            $timetable->load([
                'academicYear',
                'schoolClass',
                'section',
                'subject',
                'teacher',
            ])
        );
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return response()->json([
            'message' => 'Timetable deleted successfully.',
        ]);
    }
}
