<?php

namespace Database\Seeders;

use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\StudentEnrollment;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Default Administrator (created only if missing, so a changed password is never reset)
        $admin = User::firstOrCreate(['user_id' => 'SEC-001'], [
            'name'       => 'Registrar & Admin Office',
            'email'      => 'admin@campus.edu',
            'password'   => Hash::make('admin123'),
            'role'       => 'admin',
            'status'     => 'approved',
            'department' => 'Administration',
        ]);

        // 2. Sample Teachers
        $teacher1 = User::firstOrCreate(['user_id' => 'T-101'], [
            'name'       => 'Prof. Alan Turing',
            'email'      => 'aturing@campus.edu',
            'password'   => Hash::make('teacher123'),
            'role'       => 'teacher',
            'status'     => 'approved',
            'department' => 'College of Computer Studies',
        ]);

        $teacher2 = User::firstOrCreate(['user_id' => 'T-102'], [
            'name'       => 'Prof. Ada Lovelace',
            'email'      => 'alovelace@campus.edu',
            'password'   => Hash::make('teacher123'),
            'role'       => 'teacher',
            'status'     => 'approved',
            'department' => 'Information Technology Dept',
        ]);

        // 3. Sample Students
        $student1 = User::firstOrCreate(['user_id' => 'S-201'], [
            'name'       => 'John Doe',
            'email'      => 'jdoe@campus.edu',
            'course'     => 'BSIS',
            'section'    => '3A',
            'password'   => Hash::make('student123'),
            'role'       => 'student',
            'status'     => 'approved',
            'department' => 'College of Computer Studies',
        ]);

        $student2 = User::firstOrCreate(['user_id' => 'S-202'], [
            'name'       => 'Jane Smith',
            'email'      => 'jsmith@campus.edu',
            'course'     => 'BSIS',
            'section'    => '3A',
            'password'   => Hash::make('student123'),
            'role'       => 'student',
            'status'     => 'approved',
            'department' => 'College of Computer Studies',
        ]);

        $student3 = User::firstOrCreate(['user_id' => 'S-203'], [
            'name'       => 'Alex Cruz',
            'email'      => 'acruz@campus.edu',
            'course'     => 'BSCS',
            'section'    => '2B',
            'password'   => Hash::make('student123'),
            'role'       => 'student',
            'status'     => 'approved',
            'department' => 'College of Computer Studies',
        ]);

        // Pending user for admin testing
        User::firstOrCreate(['user_id' => 'S-204'], [
            'name'       => 'Samantha Wells',
            'email'      => 'swells@campus.edu',
            'course'     => 'BSIS',
            'section'    => '3A',
            'password'   => Hash::make('student123'),
            'role'       => 'student',
            'status'     => 'pending',
        ]);

        // Sample data was already seeded on a previous deploy, so stop here
        if (Resource::exists()) {
            return;
        }

        // 4. Sample Resources
        $room204 = Resource::create(['name' => 'Room 204', 'type' => 'room', 'location' => 'Building B, 2nd Floor', 'capacity' => 45]);
        $lab101  = Resource::create(['name' => 'Computer Lab 101', 'type' => 'lab', 'location' => 'Building A, 1st Floor', 'capacity' => 35]);
        $lab102  = Resource::create(['name' => 'Network Lab 102', 'type' => 'lab', 'location' => 'Building A, 1st Floor', 'capacity' => 30]);
        $auditorium = Resource::create(['name' => 'Main Auditorium', 'type' => 'facility', 'location' => 'University Center', 'capacity' => 250]);
        $avrRoom = Resource::create(['name' => 'AVR Room 1', 'type' => 'facility', 'location' => 'Building C, 3rd Floor', 'capacity' => 60]);
        $projectorA = Resource::create(['name' => 'Epson Projector Set A', 'type' => 'equipment', 'location' => 'AV Storage Room', 'capacity' => null]);

        // 5. Semester Class Schedules
        $cs1 = ClassSchedule::create([
            'teacher_id'    => $teacher1->id,
            'subject_code'  => 'IT301',
            'subject_name'  => 'Web Systems & Technologies',
            'section'       => 'BSIS 3A',
            'semester'      => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id'   => $room204->id,
            'room_name'     => 'Room 204',
            'days'          => 'MWF (Monday, Wednesday, Friday)',
            'start_time'    => '09:00:00',
            'end_time'      => '10:30:00',
            'notes'         => 'Bring laptops and setup development environments.',
        ]);

        $cs2 = ClassSchedule::create([
            'teacher_id'    => $teacher1->id,
            'subject_code'  => 'IT302',
            'subject_name'  => 'Database Systems & Administration',
            'section'       => 'BSIS 3A',
            'semester'      => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id'   => $lab101->id,
            'room_name'     => 'Computer Lab 101',
            'days'          => 'MWF (Monday, Wednesday, Friday)',
            'start_time'    => '11:00:00',
            'end_time'      => '12:30:00',
            'notes'         => 'SQL practical sessions conducted in Lab 101.',
        ]);

        $cs3 = ClassSchedule::create([
            'teacher_id'    => $teacher2->id,
            'subject_code'  => 'IT303',
            'subject_name'  => 'Information Assurance & Security',
            'section'       => 'BSIS 3A',
            'semester'      => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id'   => $room204->id,
            'room_name'     => 'Room 204',
            'days'          => 'TTH (Tuesday, Thursday)',
            'start_time'    => '13:30:00',
            'end_time'      => '15:00:00',
            'notes'         => 'Covers cybersecurity frameworks and ethical hacking basics.',
        ]);

        $cs4 = ClassSchedule::create([
            'teacher_id'    => $teacher2->id,
            'subject_code'  => 'CS201',
            'subject_name'  => 'Data Structures and Algorithms',
            'section'       => 'BSCS 2B',
            'semester'      => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id'   => $lab102->id,
            'room_name'     => 'Network Lab 102',
            'days'          => 'MWF (Monday, Wednesday, Friday)',
            'start_time'    => '08:00:00',
            'end_time'      => '09:30:00',
            'notes'         => 'C++ programming environment required.',
        ]);

        $cs5 = ClassSchedule::create([
            'teacher_id'    => $teacher1->id,
            'subject_code'  => 'CS202',
            'subject_name'  => 'Object-Oriented Programming',
            'section'       => 'BSIS 1A',
            'semester'      => '1st Semester',
            'academic_year' => '2026-2027',
            'resource_id'   => $lab101->id,
            'room_name'     => 'Computer Lab 101',
            'days'          => 'TTH (Tuesday, Thursday)',
            'start_time'    => '10:00:00',
            'end_time'      => '11:30:00',
            'notes'         => 'Java development and design patterns.',
        ]);

        // 6. Student Enrollments (Auto-generate schedules)
        StudentEnrollment::create(['student_id' => $student1->id, 'class_schedule_id' => $cs1->id]);
        StudentEnrollment::create(['student_id' => $student1->id, 'class_schedule_id' => $cs2->id]);
        StudentEnrollment::create(['student_id' => $student1->id, 'class_schedule_id' => $cs3->id]);

        StudentEnrollment::create(['student_id' => $student2->id, 'class_schedule_id' => $cs1->id]);
        StudentEnrollment::create(['student_id' => $student2->id, 'class_schedule_id' => $cs2->id]);
        StudentEnrollment::create(['student_id' => $student2->id, 'class_schedule_id' => $cs3->id]);

        StudentEnrollment::create(['student_id' => $student3->id, 'class_schedule_id' => $cs4->id]);
        StudentEnrollment::create(['student_id' => $student3->id, 'class_schedule_id' => $cs5->id]);

        // 7. System & Teacher Notifications
        SystemNotification::create([
            'created_by' => $admin->id,
            'title'      => 'Official Start of Academic Year 2026-2027',
            'message'    => 'Welcome faculty and students! Classroom assignments and visual timelines are now available on your dashboard.',
        ]);

        SystemNotification::create([
            'created_by' => $admin->id,
            'title'      => 'Campus Wi-Fi Maintenance Notice',
            'message'    => 'Scheduled maintenance on Building A Wi-Fi network this Saturday from 8:00 AM to 12:00 PM.',
        ]);

        UserNotification::create([
            'sender_id'      => $teacher1->id,
            'recipient_type' => 'section',
            'target_section' => 'BSIS 3A',
            'title'          => 'Welcome to IT301 Web Systems!',
            'message'        => 'Please make sure to have Node.js and VS Code installed on your laptops before our next laboratory session.',
        ]);

        UserNotification::create([
            'sender_id'      => $teacher2->id,
            'recipient_type' => 'student',
            'target_user_id' => $student1->id,
            'title'          => 'Information Security Project Group Leader',
            'message'        => 'Hi John, you have been selected as the lead for Group 1 in IT303. Please coordinate with your group members.',
        ]);

        // 8. Room Bookings
        BookingRequest::create([
            'resource_id' => $room204->id,
            'user_id'     => $teacher1->id,
            'date'        => now()->format('Y-m-d'),
            'start_time'  => '14:00:00',
            'end_time'    => '16:00:00',
            'purpose'     => 'IT Department Departmental Meeting & Faculty Curriculum Review',
            'status'      => 'approved',
            'decided_by'  => 'SEC-001',
            'decided_at'  => now(),
        ]);

        BookingRequest::create([
            'resource_id' => $auditorium->id,
            'user_id'     => $teacher2->id,
            'date'        => now()->addDays(2)->format('Y-m-d'),
            'start_time'  => '09:00:00',
            'end_time'    => '12:00:00',
            'purpose'     => 'College of Computer Studies General Student Assembly & Orientation',
            'status'      => 'pending',
        ]);
    }
}