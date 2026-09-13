<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('exam_id')
                ->constrained('exams')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            $table->decimal('marks', 8, 2);

            $table->decimal('total_marks', 8, 2);

            $table->string('grade')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'exam_id',
                'student_id',
                'subject_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};