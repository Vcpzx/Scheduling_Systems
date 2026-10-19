<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\StudentEnrollment;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();
        $tab = $request->get('tab', 'schedule');

        // Get student's enrolled class schedules
        $enrolledClasses = $student->enrolledClasses()
            ->with(['teacher', 'resource'])
            ->orderBy('start_time')
            ->get();

        // Calculate active current class and upcoming class for the student
        $now = Carbon::now();
        $todayName = $now->format('l');

        $todayClasses = $enrolledClasses->filter(function ($cs) use ($todayName) {
            return $cs->matchesDay($todayName);
        })->sortBy('start_time');

        // Check if any class is currently happening (GREEN HIGHLIGHT)
        $currentClass = $todayClasses->first(fn($cs) => $cs->isCurrentClass());
        $upcomingClass = $todayClasses->first(fn($cs) => $cs->isUpcomingToday() || $cs->isStartingSoon());

        // Available classes for enrollment (grouped by section and semester)
        $selectedSemester = $request->get('semester', '1st Semester');
        $selectedYear = $request->get('academic_year', '2026-2027');

        $availableOfferings = ClassSchedule::with(['teacher', 'resource'])
            ->when($selectedSemester !== 'all', fn($q) => $q->where('semester', $selectedSemester))
            ->when($selectedYear !== 'all', fn($q) => $q->where('academic_year', $selectedYear))
            ->orderBy('section')
            ->orderBy('subject_code')
            ->get();

        // Enrolled class schedule IDs for quick lookup
        $enrolledScheduleIds = $enrolledClasses->pluck('id')->toArray();

        // Student Notifications Inbox
        $systemNotifications = SystemNotification::latest()->take(10)->get();
        $directNotifications = $student->receivedNotifications()
            ->with('sender')
            ->take(20)
            ->get();

        // Campus Visual Schedule (Resource view)
        $scheduleDate = $request->get('date', now()->format('Y-m-d'));
        $scheduleType = $request->get('type', 'all');

        $scheduleResourcesQuery = Resource::query();
        if ($scheduleType !== 'all') {
            $scheduleResourcesQuery->where('type', $scheduleType);
        }
        $scheduleResources = $scheduleResourcesQuery->get();

        $campusBookings = BookingRequest::with(['user', 'resource'])
            ->where('date', $scheduleDate)
            ->where('status', 'approved')
            ->get();

        return view('student.dashboard', compact(
            'student',
            'tab',
            'enrolledClasses',
            'enrolledScheduleIds',
            'todayClasses',
            'currentClass',
            'upcomingClass',
            'selectedSemester',
            'selectedYear',
            'availableOfferings',
            'systemNotifications',
            'directNotifications',
            'scheduleDate',
            'scheduleType',
            'scheduleResources',
            'campusBookings'
        ));
    }

    public function enrollSubject(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_schedule_id' => 'required|exists:class_schedules,id',
        ]);

        $student = $request->user();

        StudentEnrollment::firstOrCreate([
            'student_id'        => $student->id,
            'class_schedule_id' => $data['class_schedule_id'],
        ]);

        $schedule = ClassSchedule::find($data['class_schedule_id']);

        return redirect()->route('student.dashboard', ['tab' => 'registration'])
            ->with('success', "Enrolled in {$schedule->subject_code} - {$schedule->subject_name} ({$schedule->section})! Your schedule has been updated.");
    }

    public function enrollSection(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course'        => ['required', Rule::in(User::COURSES)],
            'section'       => ['required', Rule::in(User::SECTIONS)],
            'semester'      => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $student = $request->user();
        $section = trim($data['section']);
        $course = $data['course'];
        $courseSection = "{$course} {$section}";

        $query = ClassSchedule::where('section', $courseSection);
        if (!empty($data['semester']) && $data['semester'] !== 'all') {
            $query->where('semester', $data['semester']);
        }
        if (!empty($data['academic_year']) && $data['academic_year'] !== 'all') {
            $query->where('academic_year', $data['academic_year']);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            return redirect()->route('student.dashboard', ['tab' => 'registration'])
                ->with('error', "No class schedules found for section {$courseSection}.");
        }

        $student->update([
            'course' => $course,
            'section' => $section,
        ]);

        $count = 0;
        foreach ($schedules as $schedule) {
            $enrollment = StudentEnrollment::firstOrCreate([
                'student_id'        => $student->id,
                'class_schedule_id' => $schedule->id,
            ]);
            if ($enrollment->wasRecentlyCreated) {
                $count++;
            }
        }

        return redirect()->route('student.dashboard', ['tab' => 'schedule'])
            ->with('success', "Successfully registered in Section {$courseSection}! {$count} class schedules were added to your timetable.");
    }

    public function unenroll(ClassSchedule $classSchedule): RedirectResponse
    {
        $student = Auth::user();

        StudentEnrollment::where('student_id', $student->id)
            ->where('class_schedule_id', $classSchedule->id)
            ->delete();

        return redirect()->route('student.dashboard', ['tab' => 'schedule'])
            ->with('success', "Unenrolled from {$classSchedule->subject_code}.");
    }
}