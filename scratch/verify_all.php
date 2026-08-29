<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ClassSchedule;
use App\Models\BookingRequest;
use App\Models\Resource;
use App\Models\SystemNotification;
use App\Models\UserNotification;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

View::share('errors', new \Illuminate\Support\ViewErrorBag());

echo "=== 1. VERIFYING BLADE TEMPLATE RENDERING ===\n";

$admin = User::where('role', 'admin')->first();
$teacher = User::where('role', 'teacher')->first();
$student = User::where('role', 'student')->first();

if (!$admin || !$teacher || !$student) {
    echo "ERROR: Missing test users.\n";
    exit(1);
}

// 1. Auth Views
echo "- Rendering auth.login...\n";
View::make('auth.login')->render();

echo "- Rendering auth.register...\n";
View::make('auth.register')->render();

echo "- Rendering auth.forgot-password...\n";
View::make('auth.forgot-password')->render();

echo "- Rendering auth.reset-password...\n";
View::make('auth.reset-password', ['userId' => 'T-101'])->render();

// 2. Admin Dashboard
echo "- Rendering admin.dashboard (as Admin)...\n";
Auth::login($admin);
$pendingUsers = User::where('status', 'pending')->get();
$pendingBookings = BookingRequest::where('status', 'pending')->with(['user', 'resource'])->get();
$recentApprovedBookings = BookingRequest::where('status', 'approved')->with(['user', 'resource'])->take(5)->get();
$resources = Resource::all();
$allUsers = User::all();
$notifications = SystemNotification::latest()->take(5)->get();

View::make('admin.dashboard', compact('pendingUsers', 'pendingBookings', 'recentApprovedBookings', 'resources', 'allUsers', 'notifications'))->render();

// 3. Schedule Index
echo "- Rendering schedule.index (as Admin)...\n";
$date = now()->toDateString();
$type = 'all';
$bookings = BookingRequest::where('date', $date)->where('status', 'approved')->with('user')->get();
View::make('schedule.index', compact('resources', 'bookings', 'date', 'type'))->render();

// 4. Teacher Dashboard (all tabs)
echo "- Rendering teacher.dashboard (as Teacher)...\n";
Auth::login($teacher);
$allTeachingLoads = ClassSchedule::where('teacher_id', $teacher->id)->get();
$currentClass = $allTeachingLoads->first(fn($cs) => $cs->isCurrentClass());
$upcomingClass = $allTeachingLoads->first(fn($cs) => $cs->isUpcomingToday() || $cs->isStartingSoon());
$classSchedules = $allTeachingLoads;
$selectedSemester = '1st Semester';
$selectedYear = '2026-2027';
$myBookings = BookingRequest::with('resource')->where('user_id', $teacher->id)->latest()->get();
$systemNotifications = SystemNotification::latest()->take(10)->get();
$sentNotifications = UserNotification::with('targetUser')->where('sender_id', $teacher->id)->latest()->get();
$sectionsHandled = $allTeachingLoads->pluck('section')->unique()->values();
$studentsInMySections = User::where('role', 'student')->whereIn('section', $sectionsHandled)->get();

$teacherTabs = ['schedule', 'semesters', 'room-requests', 'recent-requests', 'send-notification', 'notifications', 'profile'];
foreach ($teacherTabs as $t) {
    echo "  * tab: $t\n";
    View::make('teacher.dashboard', compact(
        'teacher', 'currentClass', 'upcomingClass', 'classSchedules', 'selectedSemester', 'selectedYear',
        'resources', 'myBookings', 'systemNotifications', 'sentNotifications', 'sectionsHandled', 'studentsInMySections'
    ) + ['tab' => $t])->render();
}

// 5. Student Dashboard (all tabs)
echo "- Rendering student.dashboard (as Student)...\n";
Auth::login($student);
$enrolledClasses = $student->enrolledClasses()->with(['teacher', 'resource'])->get();
$currentClass = $enrolledClasses->first(fn($cs) => $cs->isCurrentClass());
$upcomingClass = $enrolledClasses->first(fn($cs) => $cs->isUpcomingToday());
$availableSections = ClassSchedule::pluck('section')->unique()->values();
$availableOfferings = ClassSchedule::with(['teacher', 'resource'])->get();
$enrolledScheduleIds = $enrolledClasses->pluck('id')->toArray();
$directNotifications = $student->receivedNotifications()->latest()->get();
$scheduleDate = now()->toDateString();
$scheduleType = 'all';
$scheduleResources = Resource::all();
$campusBookings = BookingRequest::where('date', $scheduleDate)->where('status', 'approved')->with('user')->get();

$studentTabs = ['schedule', 'registration', 'notifications', 'campus-schedule', 'profile'];
foreach ($studentTabs as $t) {
    echo "  * tab: $t\n";
    View::make('student.dashboard', compact(
        'student', 'currentClass', 'upcomingClass', 'enrolledClasses', 'availableSections', 'availableOfferings',
        'enrolledScheduleIds', 'directNotifications', 'systemNotifications', 'scheduleDate', 'scheduleType',
        'scheduleResources', 'campusBookings'
    ) + ['tab' => $t])->render();
}

echo "=== ALL BLADE VIEWS RENDERED SUCCESSFULLY WITHOUT ERRORS! ===\n";
