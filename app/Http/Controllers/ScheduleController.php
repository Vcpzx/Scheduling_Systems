<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\SystemNotification;
use App\Support\WeeklyScheduleCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Entry point: routes authenticated user to their role-specific dashboard.
     */
    public function home(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard', ['tab' => 'schedule']);
        }
        return redirect()->route('student.dashboard', ['tab' => 'schedule']);
    }

    /**
     * Campus Master Visual Schedule (accessible by all roles).
     */
    public function index(Request $request)
    {
        $date = $request->get('date', now()->format('Y-m-d'));
        $type = $request->get('type', 'all');

        $resourcesQuery = Resource::query();
        if ($type !== 'all') {
            $resourcesQuery->where('type', $type);
        }
        $resources = $resourcesQuery->get();

        $bookings = BookingRequest::with(['user', 'resource'])
            ->where('date', $date)
            ->when($request->user()->isStudent(), fn ($query) => $query->where('status', 'approved'))
            ->get();

        $myBookings = $request->user()->bookingRequests()->with('resource')->latest()->get();
        $notifications = SystemNotification::latest()->take(6)->get();

        $calendarTerm = AcademicTerm::current();
        $calendarWeekDate = $request->validate(['week' => 'nullable|date'])['week'] ?? now()->toDateString();
        $calendarWeekStart = Carbon::parse($calendarWeekDate)->startOfWeek(Carbon::MONDAY);
        $calendarRoomId = $request->validate(['room_id' => 'nullable|integer|exists:resources,id'])['room_id'] ?? null;
        $calendarResources = Resource::whereIn('type', ['room', 'lab'])->orderBy('name')->get();

        if ($request->user()->isStudent()) {
            $calendarSchedules = $request->user()->enrolledClasses()->with(['teacher', 'resource']);
        } elseif ($request->user()->isTeacher()) {
            $calendarSchedules = ClassSchedule::with(['teacher', 'resource'])
                ->where('teacher_id', $request->user()->id);
        } else {
            $calendarSchedules = ClassSchedule::with(['teacher', 'resource']);
        }
        $calendarSchedules = $calendarSchedules
            ->when($calendarTerm, fn ($query) => $query
                ->where('semester', $calendarTerm->semester)
                ->where('academic_year', $calendarTerm->academic_year))
            ->orderBy('start_time')
            ->get();

        if ($calendarRoomId) {
            $calendarResource = $calendarResources->firstWhere('id', (int) $calendarRoomId);
            $calendarSchedules = $calendarSchedules->filter(function ($schedule) use ($calendarRoomId, $calendarResource) {
                return (string) $schedule->resource_id === (string) $calendarRoomId
                    || ($calendarResource && $schedule->room_name === $calendarResource->name);
            })->values();
        }

        $calendarDays = WeeklyScheduleCalendar::build($calendarSchedules, $calendarWeekStart);

        return view('schedule.index', compact(
            'date',
            'type',
            'resources',
            'bookings',
            'myBookings',
            'notifications',
            'calendarTerm',
            'calendarWeekStart',
            'calendarRoomId',
            'calendarResources',
            'calendarDays'
        ));
    }
}