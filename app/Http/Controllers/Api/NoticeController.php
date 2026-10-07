<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NoticeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Notice::query();

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'title',
                    'like',
                    '%'.$search.'%'
                )
                    ->orWhere(
                        'description',
                        'like',
                        '%'.$search.'%'
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Publish Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereDate(
                'publish_date',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'publish_date',
                '<=',
                $request->date_to
            );
        }

        return $this->paginateResponse(
            $query->latest('publish_date'),
            $request,
            10,
            'Notices retrieved successfully.'
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'publish_date' => [
                'required',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:publish_date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                    'expired',
                ]),
            ],
        ]);

        $notice = Notice::create($validated);

        return response()->json([
            'message' => 'Notice created successfully.',
            'data' => $notice,
        ], 201);
    }

    public function show(Notice $notice)
    {
        return response()->json([
            'message' => 'Notice retrieved successfully.',
            'data' => $notice,
        ]);
    }

    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'required',
                'string',
            ],

            'publish_date' => [
                'sometimes',
                'required',
                'date',
            ],

            'expiry_date' => [
                'nullable',
                'date',
                'after_or_equal:publish_date',
            ],

            'status' => [
                'sometimes',
                'required',
                Rule::in([
                    'draft',
                    'published',
                    'expired',
                ]),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate expiry date against final publish date
        |--------------------------------------------------------------------------
        */

        $publishDate = $validated['publish_date']
            ?? $notice->publish_date;

        if (
            ! empty($validated['expiry_date']) &&
            $validated['expiry_date'] < $publishDate
        ) {
            return response()->json([
                'message' => 'Expiry date must be on or after the publish date.',
            ], 422);
        }

        $notice->update($validated);

        return response()->json([
            'message' => 'Notice updated successfully.',
            'data' => $notice,
        ]);
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();

        return response()->json([
            'message' => 'Notice deleted successfully.',
        ]);
    }
}
