<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('subject_code');
            $table->string('subject_name');
            $table->string('section');
            $table->string('semester')->default('1st Semester');
            $table->string('academic_year')->default('2026-2027');
            $table->foreignId('resource_id')->nullable()->constrained('resources')->nullOnDelete();
            $table->string('room_name')->nullable();
            $table->string('days');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};