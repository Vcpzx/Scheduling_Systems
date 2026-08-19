<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Resource;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'pendingUsers' => User::where('status', 'pending')->latest()->get(),
            'pendingBookings' => BookingRequest::with(['user', 'resource'])->where('status', 'pending')->latest()->get(),
            'resources' => Resource::latest()->get(),
            'notifications' => SystemNotification::latest()->take(5)->get(),
        ]);
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
