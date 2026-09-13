<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();

            $table->string('school_name');

            $table->string('logo')->nullable();

            $table->string('email')->nullable();

            $table->string('phone')->nullable();

            $table->text('address')->nullable();

            $table->string('website')->nullable();

            $table->string('principal_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
