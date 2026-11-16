<?php

namespace App\Http\Controllers;

use App\Models\AcademicTerm;
use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use App\Models\StudentEnrollment;
use App\Models\SystemNotification;
use App\Models\User;
use App\Support\RoomAvailability;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $section = $request->validate([
            'section' => 'sometimes|in:dashboard,accounts,bookings,resources,notices,terms',
        ])['section'] ?? 'dashboard';

        $viewData = [
            'section' => $section,
            'pendingUsers' => User::where('status', 'pending')->latest()->get(),
            'pendingBookings' => BookingRequest::with(['user', 'resource'])->where('status', 'pending')->latest()->get(),
            'resources' => Resource::latest()->get(),
            'notifications' => SystemNotification::latest()->take(5)->get(),
            'academicTerms' => AcademicTerm::orderByDesc('starts_on')->get(),
        ];

        if ($section === 'dashboard') {
            $activeTerm = AcademicTerm::current();
            $activeSchedules = ClassSchedule::query()
                ->when($activeTerm, fn ($query) => $query
                    ->where('semester', $activeTerm->semester)
                    ->where('academic_year', $activeTerm->academic_year))
                ->get();
            $bookingStatusCounts = BookingRequest::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
            $weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $weekdayScheduleCounts = collect($weekdays)->mapWithKeys(fn ($day) => [
                $day => $activeSchedules->filter(fn ($schedule) => $schedule->matchesDay($day))->count(),
            ]);

            $viewData += [
                'activeTerm' => $activeTerm,
                'dashboardStats' => [
                    'students' => User::where('role', 'student')->where('status', 'approved')->count(),
                    'teachers' => User::where('role', 'teacher')->where('status', 'approved')->count(),
                    'active_classes' => $activeSchedules->count(),
                    'active_enrollments' => $activeTerm
                        ? StudentEnrollment::whereHas('classSchedule', fn ($query) => $query
                            ->where('semester', $activeTerm->semester)
                            ->where('academic_year', $activeTerm->academic_year))
                            ->count()
                        : 0,
                    'pending_accounts' => $viewData['pendingUsers']->count(),
                    'pending_bookings' => $viewData['pendingBookings']->count(),
                    'rooms' => Resource::whereIn('type', ['room', 'lab'])->count(),
                ],
                'weekdayScheduleCounts' => $weekdayScheduleCounts,
                'maxWeekdayScheduleCount' => max(1, $weekdayScheduleCounts->max() ?? 0),
                'bookingStatusCounts' => $bookingStatusCounts,
                'maxBookingStatusCount' => max(1, $bookingStatusCounts->max() ?? 0),
                'recentBookings' => BookingRequest::with(['user', 'resource'])->latest()->take(5)->get(),
            ];
        }

        return view('admin.dashboard', $viewData);
    }

    public function storeAcademicTerm(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'semester' => ['required', 'in:1st Semester,2nd Semester,Summer', Rule::unique('academic_terms')->where('academic_year', $request->input('academic_year'))],
            'academic_year' => 'required|string|max:20',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after_or_equal:starts_on',
        ]);

        $this->ensureAcademicTermDatesDoNotOverlap($data['starts_on'], $data['ends_on']);
        AcademicTerm::create($data);

        return redirect()->route('admin.dashboard', ['section' => 'terms'])
            ->with('success', 'Academic term dates saved.');
    }

    public function updateAcademicTerm(Request $request, AcademicTerm $academicTerm): RedirectResponse
    {
        $data = $request->validate([
            'semester' => [
                'required',
                'in:1st Semester,2nd Semester,Summer',
                Rule::unique('academic_terms')
                    ->where('academic_year', $request->input('academic_year'))
                    ->ignore($academicTerm->id),
            ],
            'academic_year' => 'required|string|max:20',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after_or_equal:starts_on',
        ]);

        $this->ensureAcademicTermDatesDoNotOverlap($data['starts_on'], $data['ends_on'], $academicTerm);
        $academicTerm->update($data);

        return redirect()->route('admin.dashboard', ['section' => 'terms'])
            ->with('success', 'Academic term dates updated.');
    }

    private function ensureAcademicTermDatesDoNotOverlap(string $startsOn, string $endsOn, ?AcademicTerm $except = null): void
    {
        $overlapExists = AcademicTerm::whereDate('starts_on', '<=', $endsOn)
            ->whereDate('ends_on', '>=', $startsOn)
            ->when($except, fn ($query) => $query->where('id', '<>', $except->id))
            ->exists();

        if ($overlapExists) {
            throw ValidationException::withMessages([
                'starts_on' => 'Academic term dates cannot overlap another configured term.',
            ]);
        }
    }

    public function decideUser(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:approved,rejected']);
        $user->update(['status' => $data['status'], 'decided_by' => $request->user()->user_id, 'decided_at' => now()]);

        return back()->with('success', "User {$data['status']} successfully.");
    }

    public function deleteUser(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);
        $user->delete();

        return back()->with('success', 'User account deleted.');
    }

    public function decideBooking(Request $request, BookingRequest $bookingRequest): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:approved,rejected']);

        if ($data['status'] === 'approved' && !RoomAvailability::isAvailable(
            $bookingRequest->resource,
            Carbon::parse($bookingRequest->date)->toDateString(),
            Carbon::parse($bookingRequest->start_time)->format('H:i'),
            Carbon::parse($bookingRequest->end_time)->format('H:i'),
            $bookingRequest->id
        )) {
            throw ValidationException::withMessages([
                'status' => 'This room is no longer available for the requested time.',
            ]);
        }

        $bookingRequest->update(['status' => $data['status'], 'decided_by' => $request->user()->user_id, 'decided_at' => now()]);

        return back()->with('success', "Booking request {$data['status']}.");
    }

    public function storeResource(Request $request): RedirectResponse
    {
        Resource::create($request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:room,lab,facility,equipment',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]));

        return back()->with('success', 'Resource created.');
    }

    public function updateResource(Request $request, Resource $resource): RedirectResponse
    {
        $resource->update($request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:room,lab,facility,equipment',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]));

        return back()->with('success', 'Resource updated.');
    }

    public function deleteResource(Resource $resource): RedirectResponse
    {
        $resource->delete();

        return back()->with('success', 'Resource deleted.');
    }

    public function storeNotification(Request $request): RedirectResponse
    {
        SystemNotification::create([
            'created_by' => $request->user()->id,
            ...$request->validate(['title' => 'required|string|max:255', 'message' => 'required|string|max:2000']),
        ]);

        return back()->with('success', 'Notification published to all users.');
    }
}
