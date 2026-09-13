<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->constrained('sections')
                ->cascadeOnDelete();

            $table->date('date');

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'leave',
            ]);

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'student_id',
                'date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};
