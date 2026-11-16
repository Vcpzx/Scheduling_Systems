<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Resource;
use App\Support\RoomAvailability;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'required|string|max:1000',
        ]);

        $resource = Resource::findOrFail($data['resource_id']);

        if (!RoomAvailability::isAvailable($resource, $data['date'], $data['start_time'], $data['end_time'])) {
            throw ValidationException::withMessages([
                'resource_id' => 'The selected room is already scheduled or requested for that time.',
            ]);
        }

        BookingRequest::create([...$data, 'user_id' => $request->user()->id]);

        return back()->with('success', 'Booking request submitted for administrator review.');
    }
}
