<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();

            $table->string('qualification')->nullable();
            $table->date('joining_date');

            $table->decimal('salary', 12, 2)->nullable();

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->string('photo')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
