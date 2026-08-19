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
    Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('user_id')->unique();
    $table->string('name');
    $table->string('password');
    $table->enum('role', ['student', 'teacher', 'secretary']);
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->string('decided_by')->nullable();
    $table->timestamp('decided_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
