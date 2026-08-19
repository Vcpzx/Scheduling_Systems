<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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

        BookingRequest::create([...$data, 'user_id' => $request->user()->id]);

        return back()->with('success', 'Booking request submitted for administrator review.');
    }
}
