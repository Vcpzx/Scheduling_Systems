<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('course')->nullable()->after('section');
        });

        DB::table('users')
            ->where('role', 'student')
            ->whereNotNull('section')
            ->orderBy('id')
            ->each(function (object $user): void {
                if (preg_match('/^(BSIS|BSCS|BSIT)\s+([1-4][A-D])$/i', trim($user->section), $matches)) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'course' => strtoupper($matches[1]),
                            'section' => strtoupper($matches[2]),
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('course');
        });
    }
};
