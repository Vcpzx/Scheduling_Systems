<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Resource;
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
            ->get();

        return view('schedule.index', compact('date', 'type', 'resources', 'bookings'));
    }
}