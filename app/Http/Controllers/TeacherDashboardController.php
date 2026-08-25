<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\SystemNotification;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();
        $tab = $request->get('tab', 'schedule');

        // Resources for room booking & schedule creation
        $resources = Resource::orderBy('name')->get();

        // Teacher's class schedules (Semester Handling)
        $selectedSemester = $request->get('semester', '1st Semester');
        $selectedYear = $request->get('academic_year', '2026-2027');

        $classSchedules = ClassSchedule::with(['resource', 'students'])
            ->where('teacher_id', $teacher->id)
            ->when($selectedSemester !== 'all', fn($q) => $q->where('semester', $selectedSemester))
            ->when($selectedYear !== 'all', fn($q) => $q->where('academic_year', $selectedYear))
            ->orderBy('start_time')
            ->get();

        $allTeachingLoads = ClassSchedule::where('teacher_id', $teacher->id)->get();
        $sectionsHandled = $allTeachingLoads->pluck('section')->unique()->values();

        // Calculate upcoming / current classes for Teacher
        $now = Carbon::now();
        $todayName = $now->format('l');

        $todayClasses = $allTeachingLoads->filter(function ($cs) use ($todayName) {
            return $cs->matchesDay($todayName);
        })->sortBy('start_time');

        $currentClass = $todayClasses->first(fn($cs) => $cs->isCurrentClass());
        $upcomingClass = $todayClasses->first(fn($cs) => $cs->isUpcomingToday() || $cs->isStartingSoon());

        // Room Booking Requests
        $myBookings = BookingRequest::with('resource')
            ->where('user_id', $teacher->id)
            ->latest()
            ->get();

        // Notifications
        $systemNotifications = SystemNotification::latest()->take(10)->get();
        $sentNotifications = UserNotification::with('targetUser')
            ->where('sender_id', $teacher->id)
            ->latest()
            ->take(15)
            ->get();

        // Students in teacher's sections for targeted notifications
        $studentsInMySections = User::where('role', 'student')
            ->whereIn('section', $sectionsHandled)
            ->orderBy('name')
            ->get();

        // Visual Campus Schedule (for the visual schedule viewer tab)
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

        return view('teacher.dashboard', compact(
            'teacher',
            'tab',
            'resources',
            'classSchedules',
            'sectionsHandled',
            'selectedSemester',
            'selectedYear',
            'todayClasses',
            'currentClass',
            'upcomingClass',
            'myBookings',
            'systemNotifications',
            'sentNotifications',
            'studentsInMySections',
            'scheduleDate',
            'scheduleType',
            'scheduleResources',
            'campusBookings'
        ));
    }

    public function storeClassSchedule(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject_code'  => 'required|string|max:50',
            'subject_name'  => 'required|string|max:255',
            'section'       => 'required|string|max:50',
            'semester'      => 'required|string|max:50',
            'academic_year' => 'required|string|max:50',
            'resource_id'   => 'nullable|exists:resources,id',
            'room_name'     => 'nullable|string|max:100',
            'days'          => 'required|string|max:100',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i|after:start_time',
            'notes'         => 'nullable|string|max:500',
        ]);

        $teacher = $request->user();

        if (!empty($data['resource_id'])) {
            $resource = Resource::find($data['resource_id']);
            if ($resource && empty($data['room_name'])) {
                $data['room_name'] = $resource->name;
            }
        }

        ClassSchedule::create([
            'teacher_id'    => $teacher->id,
            'subject_code'  => strtoupper(trim($data['subject_code'])),
            'subject_name'  => trim($data['subject_name']),
            'section'       => strtoupper(trim($data['section'])),
            'semester'      => $data['semester'],
            'academic_year' => $data['academic_year'],
            'resource_id'   => $data['resource_id'] ?: null,
            'room_name'     => $data['room_name'] ?: 'TBA',
            'days'          => $data['days'],
            'start_time'    => $data['start_time'],
            'end_time'      => $data['end_time'],
            'notes'         => $data['notes'] ?? null,
        ]);

        return redirect()->route('teacher.dashboard', ['tab' => 'semesters'])
            ->with('success', "Class schedule for {$data['subject_code']} ({$data['section']}) successfully added!");
    }

    public function deleteClassSchedule(ClassSchedule $classSchedule): RedirectResponse
    {
        abort_if($classSchedule->teacher_id !== Auth::id(), 403);
        $subject = $classSchedule->subject_code;
        $classSchedule->delete();

        return redirect()->route('teacher.dashboard', ['tab' => 'semesters'])
            ->with('success', "Schedule for {$subject} was removed.");
    }

    public function sendNotification(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'recipient_type' => 'required|in:section,student,all',
            'target_section' => 'nullable|required_if:recipient_type,section|string',
            'target_user_id' => 'nullable|required_if:recipient_type,student|exists:users,id',
            'title'          => 'required|string|max:255',
            'message'        => 'required|string|max:2000',
        ]);

        $teacher = $request->user();

        UserNotification::create([
            'sender_id'      => $teacher->id,
            'recipient_type' => $data['recipient_type'],
            'target_section' => $data['recipient_type'] === 'section' ? strtoupper(trim($data['target_section'])) : null,
            'target_user_id' => $data['recipient_type'] === 'student' ? $data['target_user_id'] : null,
            'title'          => trim($data['title']),
            'message'        => trim($data['message']),
        ]);

        $targetDesc = 'students';
        if ($data['recipient_type'] === 'section') {
            $targetDesc = "all students in section {$data['target_section']}";
        } elseif ($data['recipient_type'] === 'student') {
            $student = User::find($data['target_user_id']);
            $targetDesc = $student ? $student->name : 'the student';
        }

        return redirect()->route('teacher.dashboard', ['tab' => 'notifications'])
            ->with('success', "Notification broadcasted to {$targetDesc} successfully!");
    }
}