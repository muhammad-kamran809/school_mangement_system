<?php

use App\Models\Student;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware([
        Authenticate::class,
        PermissionMiddleware::class,
        RoleMiddleware::class,
    ]);
});

describe('student CRUD API', function () {
    it('creates a student and persists all fields', function () {
        $studentData = [
            'name' => 'Amina Khan',
            'email' => 'amina@example.com',
            'phone' => '03001234567',
            'gender' => 'female',
            'date_of_birth' => '2015-04-12',
            'address' => '12 Main Street',
            'guardian_name' => 'Imran Khan',
            'guardian_phone' => '03007654321',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/students', $studentData);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Amina Khan')
            ->assertJsonPath('data.email', 'amina@example.com');

        $this->assertDatabaseHas('students', [
            'name' => 'Amina Khan',
            'email' => 'amina@example.com',
            'phone' => '03001234567',
            'gender' => 'female',
            'guardian_name' => 'Imran Khan',
            'guardian_phone' => '03007654321',
            'status' => 'active',
        ]);
    });

    it('lists and shows students with pagination', function () {
        $student = Student::factory()->create();

        $listResponse = $this->getJson('/api/students');
        $showResponse = $this->getJson("/api/students/{$student->id}");

        $listResponse->assertOk()
            ->assertJsonPath('data.0.id', $student->id)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);

        $showResponse->assertOk()->assertJsonPath('data.id', $student->id);
    });

    it('updates a student', function () {
        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Updated Student',
            'email' => 'new_'.$student->email,
            'phone' => $student->phone,
            'gender' => $student->gender,
            'date_of_birth' => '2015-01-01',
            'address' => $student->address,
            'guardian_name' => $student->guardian_name,
            'guardian_phone' => $student->guardian_phone,
            'status' => $student->status,
        ]);

        $response->assertOk()->assertJsonPath('data.name', 'Updated Student');
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Updated Student',
        ]);
    });

    it('deletes a student', function () {
        $student = Student::factory()->create();

        $response = $this->deleteJson("/api/students/{$student->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    });

    it('rejects an empty student payload', function () {
        $response = $this->postJson('/api/students', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'gender',
                'guardian_name',
                'guardian_phone',
                'status',
            ]);
    });
});
