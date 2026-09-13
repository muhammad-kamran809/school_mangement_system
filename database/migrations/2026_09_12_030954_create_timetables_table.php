<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->cascadeOnDelete();

            $table->string('day', 20);

            $table->time('start_time');

            $table->time('end_time');

            $table->string('room')->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'academic_year_id',
                    'class_id',
                    'section_id',
                    'day',
                    'start_time'
                ],
                'timetable_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
