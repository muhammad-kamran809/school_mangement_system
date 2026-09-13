<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->date('date_of_birth')->nullable();
            $table->text('address')->nullable();

            $table->string('designation');
            $table->string('department')->nullable();

            $table->date('joining_date');

            $table->decimal('salary', 12, 2)->nullable();

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->string('photo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};