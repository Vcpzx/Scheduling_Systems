<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Resource;
use App\Models\SystemNotification;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
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

        return view('schedule.index', compact('date', 'type', 'resources', 'bookings', 'myBookings', 'notifications'));
    }
}