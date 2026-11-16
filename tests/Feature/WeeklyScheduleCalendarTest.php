<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Support\WeeklyScheduleCalendar;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WeeklyScheduleCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_calendar_limits_student_to_enrolled_classes_in_the_current_term(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-01'));
        $student = $this->createUser('student', 'student');
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $room = Resource::create(['name' => 'Room 204', 'type' => 'room']);

        $this->createTerm('1st Semester', '2026-2027', '2026-08-01', '2026-12-20');
        $this->createTerm('2nd Semester', '2026-2027', '2026-12-21', '2027-05-30');
        $currentClass = $this->createClass($teacher, $room, 'IT101', 'Current Subject', '1st Semester');
        $otherClass = $this->createClass($teacher, $room, 'IT102', 'Not Enrolled Subject', '1st Semester', 'Tuesday');
        $nextTermClass = $this->createClass($teacher, $room, 'IT201', 'Next Term Subject', '2nd Semester');

        StudentEnrollment::create(['student_id' => $student->id, 'class_schedule_id' => $currentClass->id]);
        StudentEnrollment::create(['student_id' => $student->id, 'class_schedule_id' => $nextTermClass->id]);

        $response = $this->actingAs($student)->get(route('student.dashboard', ['tab' => 'schedule']));

        $response->assertOk()
            ->assertSee('IT101')
            ->assertSee('Current Subject')
            ->assertSee('Room 204')
            ->assertSee('Teacher Name')
            ->assertDontSee('IT102')
            ->assertDontSee('IT201')
            ->assertSee('7:00 AM')
            ->assertSee('5:00 PM')
            ->assertDontSee('title="Schedules"', false);

        Carbon::setTestNow(Carbon::parse('2026-12-21'));
        $this->get(route('student.dashboard', ['tab' => 'schedule']))
            ->assertOk()
            ->assertSee('IT201')
            ->assertDontSee('IT101');
    }

    public function test_teacher_calendar_shows_only_its_teaching_loads_while_admin_sees_all(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-01'));
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $otherTeacher = $this->createUser('teacher', 'Other Professor');
        $admin = $this->createUser('admin', 'Admin Name');
        $room = Resource::create(['name' => 'Room 204', 'type' => 'room']);

        $this->createTerm('1st Semester', '2026-2027', '2026-08-01', '2026-12-20');
        $this->createClass($teacher, $room, 'IT101', 'Teacher Class', '1st Semester');
        $this->createClass($otherTeacher, $room, 'IT102', 'Other Teacher Class', '1st Semester', 'Tuesday');
        $this->createClass($teacher, $room, 'IT201', 'Next Term Class', '2nd Semester');

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard', ['tab' => 'schedule']))
            ->assertOk()
            ->assertSee('IT101')
            ->assertDontSee('IT102')
            ->assertDontSee('IT201')
            ->assertDontSee('title="Schedules"', false)
            ->assertDontSee('id="csSidebarToggle"', false);

        $this->actingAs($teacher)
            ->get(route('schedule.index'))
            ->assertOk()
            ->assertSee('IT101')
            ->assertDontSee('IT102')
            ->assertSee('Visual Resource Occupancy');

        $this->actingAs($admin)
            ->get(route('schedule.index'))
            ->assertOk()
            ->assertSee('IT101')
            ->assertSee('IT102')
            ->assertDontSee('IT201')
            ->assertSee('All rooms')
            ->assertDontSee('Visual Resource Occupancy')
            ->assertSee('title="Schedules"', false)
            ->assertSee('id="csSidebarToggle"', false);
    }

    public function test_class_schedule_creation_rejects_room_teacher_or_section_conflicts(): void
    {
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $otherTeacher = $this->createUser('teacher', 'Other Teacher');
        $room = Resource::create(['name' => 'Room 204', 'type' => 'room']);
        $otherRoom = Resource::create(['name' => 'Room 305', 'type' => 'room']);
        $this->createClass($teacher, $room, 'IT101', 'Existing Class', '1st Semester');

        $conflictingSchedules = [
            [$teacher, $otherRoom, 'BSIS 2A'],
            [$otherTeacher, $room, 'BSIS 2A'],
            [$otherTeacher, $otherRoom, 'BSIS 1A'],
        ];

        foreach ($conflictingSchedules as $index => [$scheduleTeacher, $scheduleRoom, $section]) {
            $this->actingAs($scheduleTeacher)
                ->from(route('teacher.dashboard', ['tab' => 'semesters']))
                ->post(route('teacher.schedules.store'), $this->schedulePayload(
                    'IT10'.($index + 2),
                    $section,
                    $scheduleRoom,
                ))
                ->assertRedirect(route('teacher.dashboard', ['tab' => 'semesters']))
                ->assertSessionHasErrors('start_time');
        }

        $this->assertSame(1, ClassSchedule::count());

        $this->actingAs($otherTeacher)
            ->post(route('teacher.schedules.store'), $this->schedulePayload('IT105', 'BSIS 2A', $otherRoom))
            ->assertRedirect(route('teacher.dashboard', ['tab' => 'semesters']))
            ->assertSessionHasNoErrors();

        $this->assertSame(2, ClassSchedule::count());
    }

    public function test_room_requests_exclude_busy_rooms_and_reject_conflicts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-01'));
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $admin = $this->createUser('admin', 'Admin Name');
        $occupiedRoom = Resource::create(['name' => 'Room 204', 'type' => 'room']);
        $availableRoom = Resource::create(['name' => 'Room 305', 'type' => 'room']);
        $this->createTerm('1st Semester', '2026-2027', '2026-08-01', '2026-12-20');
        $this->createClass($teacher, $occupiedRoom, 'IT101', 'Existing Class', '1st Semester');

        $availabilityUrl = route('teacher.dashboard', [
            'tab' => 'room-requests',
            'date' => '2026-10-05',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);
        $this->actingAs($teacher)
            ->get($availabilityUrl)
            ->assertOk()
            ->assertSee('Room 305')
            ->assertDontSee('Room 204');

        $this->from($availabilityUrl)
            ->post(route('bookings.store'), $this->bookingPayload($occupiedRoom))
            ->assertSessionHasErrors('resource_id');

        $this->post(route('bookings.store'), $this->bookingPayload($availableRoom))
            ->assertSessionHasNoErrors();
        $booking = BookingRequest::firstOrFail();
        $this->assertSame('pending', $booking->status);

        $this->actingAs($admin)
            ->patch(route('admin.bookings.decide', $booking), ['status' => 'approved'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('booking_requests', ['id' => $booking->id, 'status' => 'approved']);

        $this->actingAs($teacher)
            ->from($availabilityUrl)
            ->post(route('bookings.store'), $this->bookingPayload($availableRoom))
            ->assertSessionHasErrors('resource_id');
    }

    public function test_active_term_changes_to_the_next_term_after_the_current_one_ends(): void
    {
        $this->createTerm('1st Semester', '2026-2027', '2026-08-01', '2026-12-20');
        $nextTerm = $this->createTerm('2nd Semester', '2026-2027', '2026-12-21', '2027-05-30');

        Carbon::setTestNow(Carbon::parse('2026-12-20'));
        $this->assertSame('1st Semester', AcademicTerm::current()->semester);

        Carbon::setTestNow(Carbon::parse('2026-12-21'));
        $this->assertSame($nextTerm->id, AcademicTerm::current()->id);
    }

    public function test_admin_can_save_academic_term_dates(): void
    {
        $admin = $this->createUser('admin', 'Admin Name');

        $this->actingAs($admin)
            ->post(route('admin.academic-terms.store'), [
                'semester' => '1st Semester',
                'academic_year' => '2026-2027',
                'starts_on' => '2026-08-01',
                'ends_on' => '2026-12-20',
            ])
            ->assertRedirect(route('admin.dashboard', ['section' => 'terms']));

        $term = AcademicTerm::firstOrFail();
        $this->assertSame('1st Semester', $term->semester);
        $this->assertSame('2026-2027', $term->academic_year);
        $this->assertSame('2026-08-01', $term->starts_on->toDateString());
        $this->assertSame('2026-12-20', $term->ends_on->toDateString());

        $this->get(route('admin.dashboard', ['section' => 'terms']))
            ->assertOk()
            ->assertSee('Configure Academic Terms')
            ->assertSee('2026-2027');
    }

    public function test_admin_dashboard_shows_term_summary_and_charts(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-10-01'));
        $admin = $this->createUser('admin', 'Admin Name');
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $student = $this->createUser('student', 'Student Name');
        $room = Resource::create(['name' => 'Room 204', 'type' => 'room']);
        $this->createTerm('1st Semester', '2026-2027', '2026-08-01', '2026-12-20');
        $schedule = $this->createClass($teacher, $room, 'IT101', 'Current Subject', '1st Semester');
        StudentEnrollment::create(['student_id' => $student->id, 'class_schedule_id' => $schedule->id]);
        BookingRequest::create([
            'resource_id' => $room->id,
            'user_id' => $teacher->id,
            'date' => '2026-10-05',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'purpose' => 'Faculty meeting',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSee('Approved Students')
            ->assertSee('Classes This Term')
            ->assertSee('Weekly Class Distribution')
            ->assertSee('Monday: 1 classes', false)
            ->assertSee('Room Request Status')
            ->assertSee('Recent Room Requests')
            ->assertSee('1 student enrollments')
            ->assertSee('title="Admin Dashboard"', false)
            ->assertSee('cs-nav-item active" title="Admin Dashboard"', false)
            ->assertDontSee('cs-nav-item active" title="Accounts Awaiting Review"', false);

        $this->get(route('admin.dashboard', ['section' => 'accounts']))
            ->assertOk()
            ->assertSee('cs-nav-item active" title="Accounts Awaiting Review"', false)
            ->assertDontSee('cs-nav-item active" title="Admin Dashboard"', false);

        $this->get(route('home'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_overlapping_classes_get_separate_calendar_lanes(): void
    {
        $teacher = $this->createUser('teacher', 'Teacher Name');
        $room = Resource::create(['name' => 'Room 204', 'type' => 'room']);
        $first = $this->createClass($teacher, $room, 'IT101', 'First Subject', '1st Semester');
        $second = $this->createClass($teacher, $room, 'IT102', 'Second Subject', '1st Semester');
        $weekStart = Carbon::parse('2026-10-05')->startOfWeek(Carbon::MONDAY);

        $mondayEvents = WeeklyScheduleCalendar::build(collect([$first, $second]), $weekStart)[0]['events'];

        $this->assertCount(2, $mondayEvents);
        $this->assertSame([0, 1], array_column($mondayEvents, 'lane'));
        $this->assertSame([2, 2], array_column($mondayEvents, 'lane_count'));
    }

    private function createUser(string $role, string $name): User
    {
        static $userNumber = 0;
        $userNumber++;

        return User::create([
            'user_id' => strtoupper(substr($role, 0, 1)).'-'.$userNumber,
            'name' => $name,
            'email' => strtolower($role).$userNumber.'@example.com',
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'approved',
        ]);
    }

    private function createTerm(string $semester, string $year, string $start, string $end): AcademicTerm
    {
        return AcademicTerm::create([
            'semester' => $semester,
            'academic_year' => $year,
            'starts_on' => $start,
            'ends_on' => $end,
        ]);
    }

    private function createClass(
        User $teacher,
        Resource $room,
        string $code,
        string $name,
        string $semester,
        string $days = 'Monday'
    ): ClassSchedule {
        return ClassSchedule::create([
            'teacher_id' => $teacher->id,
            'subject_code' => $code,
            'subject_name' => $name,
            'section' => 'BSIS 1A',
            'semester' => $semester,
            'academic_year' => '2026-2027',
            'resource_id' => $room->id,
            'room_name' => $room->name,
            'days' => $days,
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);
    }

    private function schedulePayload(string $code, string $section, Resource $room): array
    {
        return [
            'subject_code' => $code,
            'subject_name' => 'New Subject',
            'section' => $section,
            'semester' => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id' => $room->id,
            'days' => 'Monday',
            'start_time' => '08:30',
            'end_time' => '09:00',
        ];
    }

    private function bookingPayload(Resource $room): array
    {
        return [
            'resource_id' => $room->id,
            'date' => '2026-10-05',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'purpose' => 'Classroom activity',
        ];
    }
}
