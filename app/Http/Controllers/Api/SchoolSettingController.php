<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolSettingController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Get School Settings
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $settings = SchoolSetting::first();

        return response()->json([
            'message' => 'School settings retrieved successfully.',
            'data' => $settings,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create School Settings
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Prevent Multiple Settings Records
        |--------------------------------------------------------------------------
        */

        if (SchoolSetting::exists()) {
            return response()->json([
                'message' => 'School settings already exist. Use update instead.',
            ], 422);
        }

        $validated = $request->validate([
            'school_name' => [
                'required',
                'string',
                'max:255',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'principal_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload Logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request
                ->file('logo')
                ->store('school-settings', 'public');
        }

        $settings = SchoolSetting::create($validated);

        return response()->json([
            'message' => 'School settings created successfully.',
            'data' => $settings,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Settings
    |--------------------------------------------------------------------------
    */

    public function show()
    {
        $settings = SchoolSetting::first();

        if (!$settings) {
            return response()->json([
                'message' => 'School settings not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'School settings retrieved successfully.',
            'data' => $settings,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Settings
    |--------------------------------------------------------------------------
    */

    public function update(Request $request)
    {
        $settings = SchoolSetting::first();

        if (!$settings) {
            return response()->json([
                'message' => 'School settings not found.',
            ], 404);
        }

        $validated = $request->validate([
            'school_name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'principal_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Replace Logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            if (
                $settings->logo &&
                Storage::disk('public')->exists($settings->logo)
            ) {
                Storage::disk('public')->delete(
                    $settings->logo
                );
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store('school-settings', 'public');
        }

        $settings->update($validated);

        return response()->json([
            'message' => 'School settings updated successfully.',
            'data' => $settings,
        ]);
    }
}
