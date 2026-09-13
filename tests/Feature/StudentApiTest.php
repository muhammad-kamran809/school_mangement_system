<?php

use App\Models\Student;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

describe('student CRUD API', function () {
    it('creates a student and persists all fields', function () {
        $studentData = [
            'name' => 'Amina Khan',
            'email' => 'amina@example.com',
            'phone' => '03001234567',
            'class_name' => '5',
            'section' => 'A',
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
        $this->assertDatabaseHas('students', $studentData);
    });

    it('lists and shows students', function () {
        $student = Student::factory()->create();

        $listResponse = $this->getJson('/api/students');
        $showResponse = $this->getJson("/api/students/{$student->id}");

        $listResponse->assertOk()->assertJsonPath('data.0.id', $student->id);
        $showResponse->assertOk()->assertJsonPath('data.id', $student->id);
    });

    it('updates a student', function () {
        $student = Student::factory()->create();

        $response = $this->putJson("/api/students/{$student->id}", [
            'name' => 'Updated Student',
            'email' => $student->email,
            'phone' => $student->phone,
            'class_name' => $student->class_name,
            'section' => $student->section,
            'gender' => $student->gender,
            'date_of_birth' => $student->date_of_birth,
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

        $response->assertNoContent();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    });

    it('rejects an empty student payload', function () {
        $response = $this->postJson('/api/students', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'email',
                'phone',
                'class_name',
                'section',
                'gender',
                'date_of_birth',
                'address',
                'guardian_name',
                'guardian_phone',
                'status',
            ]);
    });
});
