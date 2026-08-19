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
    Schema::create('booking_requests', function (Blueprint $table) {
        $table->id();
        $table->foreignId('resource_id')->constrained('resources')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->date('date');
        $table->time('start_time');
        $table->time('end_time');
        $table->text('purpose');
        $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
        $table->string('decided_by')->nullable();
        $table->timestamp('decided_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
