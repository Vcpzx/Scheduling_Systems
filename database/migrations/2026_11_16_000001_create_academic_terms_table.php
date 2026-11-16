<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('semester');
            $table->string('academic_year');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->timestamps();

            $table->unique(['semester', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
    }
};
