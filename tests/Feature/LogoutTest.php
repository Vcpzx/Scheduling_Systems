<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_log_out(): void
    {
        $student = User::query()->create([
            'user_id' => 'S-100',
            'name' => 'Test Student',
            'role' => 'student',
            'status' => 'approved',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($student)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
