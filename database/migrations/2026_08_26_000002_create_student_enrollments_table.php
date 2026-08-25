<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('class_schedule_id')->constrained('class_schedules')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['student_id', 'class_schedule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};