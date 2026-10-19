<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_has_separate_course_and_section_options(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk()
            ->assertSee('name="course"', false)
            ->assertSee('name="section"', false)
            ->assertSee('>BSIS</option>', false)
            ->assertSee('>BSCS</option>', false)
            ->assertSee('>BSIT</option>', false)
            ->assertSee('>1A</option>', false)
            ->assertSee('>4D</option>', false);
    }

    public function test_student_registration_saves_course_and_section_separately(): void
    {
        $response = $this->post(route('register'), [
            'user_id' => 'S-300',
            'name' => 'Test Student',
            'email' => 'test.student@example.com',
            'role' => 'student',
            'course' => 'BSCS',
            'section' => '2B',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('login'));

        $student = User::where('user_id', 'S-300')->firstOrFail();
        $this->assertSame('BSCS', $student->course);
        $this->assertSame('2B', $student->section);
        $this->assertSame('BSCS 2B', $student->course_section);
    }

    public function test_student_registration_rejects_sections_outside_the_options(): void
    {
        $response = $this->from(route('register'))->post(route('register'), [
            'user_id' => 'S-301',
            'name' => 'Test Student',
            'role' => 'student',
            'course' => 'BSIS',
            'section' => '5A',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('section');
        $this->assertDatabaseMissing('users', ['user_id' => 'S-301']);
    }
}
