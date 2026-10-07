<?php

use App\Models\AcademicYear;
use App\Models\Event;
use App\Models\Notice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware();
});

describe('API Pagination Support', function () {
    it('paginates students with metadata and custom per_page', function () {
        Student::factory()->count(15)->create();

        $response = $this->getJson('/api/students?per_page=5&page=2');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination.current_page', 2)
            ->assertJsonPath('pagination.per_page', 5)
            ->assertJsonPath('pagination.total', 15)
            ->assertJsonPath('pagination.last_page', 3)
            ->assertJsonCount(5, 'data');
    });

    it('returns all students when all=true is passed', function () {
        Student::factory()->count(12)->create();

        $response = $this->getJson('/api/students?all=true');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pagination', null)
            ->assertJsonCount(12, 'data');
    });

    it('paginates academic years', function () {
        AcademicYear::create([
            'name' => '2024-2025',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
            'status' => 'active',
        ]);
        AcademicYear::create([
            'name' => '2025-2026',
            'start_date' => '2025-01-01',
            'end_date' => '2025-12-31',
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/academic-years?per_page=1');

        $response->assertOk()
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonPath('pagination.per_page', 1)
            ->assertJsonCount(1, 'data');
    });

    it('paginates classes', function () {
        SchoolClass::create(['name' => 'Grade 1', 'status' => 'active']);
        SchoolClass::create(['name' => 'Grade 2', 'status' => 'active']);
        SchoolClass::create(['name' => 'Grade 3', 'status' => 'active']);

        $response = $this->getJson('/api/classes?per_page=2&page=1');

        $response->assertOk()
            ->assertJsonPath('pagination.total', 3)
            ->assertJsonPath('pagination.last_page', 2)
            ->assertJsonCount(2, 'data');
    });

    it('paginates subjects', function () {
        Subject::create(['name' => 'Mathematics', 'code' => 'MATH101', 'status' => 'active']);
        Subject::create(['name' => 'Physics', 'code' => 'PHY101', 'status' => 'active']);

        $response = $this->getJson('/api/subjects?per_page=1&page=1');

        $response->assertOk()
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonCount(1, 'data');
    });

    it('paginates notices and events', function () {
        Notice::create([
            'title' => 'Notice 1',
            'description' => 'Test',
            'publish_date' => now()->toDateString(),
            'status' => 'published',
        ]);
        Notice::create([
            'title' => 'Notice 2',
            'description' => 'Test 2',
            'publish_date' => now()->toDateString(),
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/notices?per_page=1');

        $response->assertOk()
            ->assertJsonPath('pagination.total', 2)
            ->assertJsonCount(1, 'data');

        Event::create([
            'title' => 'Sports Gala',
            'description' => 'Annual sports',
            'event_date' => now()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '15:00:00',
            'location' => 'Main Ground',
            'status' => 'scheduled',
        ]);

        $eventResponse = $this->getJson('/api/events');
        $eventResponse->assertOk()
            ->assertJsonPath('pagination.total', 1)
            ->assertJsonCount(1, 'data');
    });

    it('paginates users', function () {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/users?per_page=3');

        $response->assertOk()
            ->assertJsonPath('pagination.per_page', 3)
            ->assertJsonCount(3, 'data');
    });
});
