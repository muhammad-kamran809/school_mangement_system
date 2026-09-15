<?php

use App\Models\StudentParent;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('assigns a student to a parent when creating the student', function () {
    $user = User::factory()->create();
    $parent = StudentParent::create([
        'user_id' => $user->id,
        'name' => 'Imran Khan',
    ]);

    $this->withoutMiddleware();

    $response = $this->postJson('/api/students', [
        'name' => 'Amina Khan',
        'student_parents_id' => $parent->id,
        'gender' => 'female',
        'guardian_name' => 'Imran Khan',
        'guardian_phone' => '03007654321',
        'status' => 'active',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.student_parents_id', $parent->id);

    $this->assertDatabaseHas('students', [
        'name' => 'Amina Khan',
        'student_parents_id' => $parent->id,
    ]);
});
