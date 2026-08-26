@extends('layouts.app')

@section('title', 'Admin Workspace | Campus Scheduler')

@section('content')
<div class="cs-header-banner">
  <div>
    <h1>Admin Workspace</h1>
    <p>Manage account registrations, approve space reservations, configure campus resources, and broadcast system notifications.</p>
  </div>
  <div>
    <span class="cs-badge cs-badge-approved" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Admin Mode Active</span>
  </div>
</div>

<div class="cs-grid-2" style="align-items: start; margin-bottom: 2rem;">
  {{-- Pending User Accounts --}}
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Accounts Awaiting Review</h2>
        <p class="cs-panel-subtitle">New teacher and student registration requests</p>
      </div>
      <span class="cs-badge cs-badge-warning">{{ $pendingUsers->count() }} Pending</span>
    </div>

    @if($pendingUsers->isEmpty())
      <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No accounts currently pending approval.</p>
    @else
      <div class="cs-table-container">
        <table class="cs-table">
          <thead>
            <tr>
              <th>User Info</th>
              <th>Role</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pendingUsers as $user)
              <tr>
                <td>
                  <strong>{{ $user->name }}</strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $user->user_id }}{{ $user->section ? ' · '.$user->section : '' }}</div>
                  @if($user->email)<div style="font-size: 0.75rem; color: var(--text-light);">{{ $user->email }}</div>@endif
                </td>
                <td>
                  <span class="cs-badge cs-badge-neutral">{{ ucfirst($user->role) }}</span>
                </td>
                <td>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <form method="POST" action="{{ route('admin.users.decide', $user) }}" style="display:inline">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="approved">
                      <button class="cs-button cs-button-success cs-button-sm" type="submit">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.decide', $user) }}" style="display:inline">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="rejected">
                      <button class="cs-button cs-button-warning cs-button-sm" type="submit">Deny</button>
                    </form>
                    <form method="POST" action="{{ route('admin.users.delete', $user) }}" style="display:inline" onsubmit="return confirm('Delete this account completely?');">
                      @csrf
                      @method('DELETE')
                      <button class="cs-button cs-button-danger cs-button-sm" type="submit">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- Pending Booking Requests --}}
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Space Booking Requests</h2>
        <p class="cs-panel-subtitle">Room and facility requests from teachers</p>
      </div>
      <span class="cs-badge cs-badge-warning">{{ $pendingBookings->count() }} Pending</span>
    </div>

    @if($pendingBookings->isEmpty())
      <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No pending booking requests in the queue.</p>
    @else
      <div class="cs-table-container">
        <table class="cs-table">
          <thead>
            <tr>
              <th>Resource & Teacher</th>
              <th>Date & Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pendingBookings as $booking)
              <tr>
                <td>
                  <strong>{{ $booking->resource->name }}</strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $booking->user->name }} · {{ $booking->purpose }}</div>
                </td>
                <td>
                  <div><strong>{{ date('M d, Y', strtotime($booking->date)) }}</strong></div>
                  <div style="font-size: 0.8rem; color: var(--text-muted);">{{ date('g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</div>
                </td>
                <td>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <form method="POST" action="{{ route('admin.bookings.decide', $booking) }}" style="display:inline">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="approved">
                      <button class="cs-button cs-button-success cs-button-sm" type="submit">Approve</button>
                    </form>
                    <form method="POST" action="{{ route('admin.bookings.decide', $booking) }}" style="display:inline">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="status" value="rejected">
                      <button class="cs-button cs-button-danger cs-button-sm" type="submit">Decline</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<div class="cs-grid-2" style="align-items: start;">
  {{-- Campus Resources Management --}}
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Campus Resources Management</h2>
        <p class="cs-panel-subtitle">Add and configure rooms, labs, and equipment</p>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.resources.store') }}" class="cs-form-grid" style="margin-bottom: 1.5rem;">
      @csrf
      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="res_name">Resource Name</label>
          <input class="cs-input" id="res_name" name="name" placeholder="e.g. Room 302, Cyber Lab" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="res_type">Type</label>
          <select class="cs-select" id="res_type" name="type" required>
            <option value="room">Classroom</option>
            <option value="lab">Laboratory</option>
            <option value="facility">Facility</option>
            <option value="equipment">Equipment</option>
          </select>
        </div>
      </div>

      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="res_location">Building / Location</label>
          <input class="cs-input" id="res_location" name="location" placeholder="e.g. Building B, 2nd Floor">
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="res_capacity">Seat Capacity</label>
          <input class="cs-input" type="number" id="res_capacity" name="capacity" min="1" placeholder="e.g. 45">
        </div>
      </div>

      <button type="submit" class="cs-button">Add Resource</button>
    </form>

    <div class="cs-table-container">
      <table class="cs-table">
        <thead>
          <tr>
            <th>Resource</th>
            <th>Type & Location</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($resources as $res)
            <tr>
              <td><strong>{{ $res->name }}</strong></td>
              <td>
                <span class="cs-badge cs-badge-neutral">{{ strtoupper($res->type) }}</span>
                <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.25rem;">{{ $res->location }}{{ $res->capacity ? ' ('.$res->capacity.' seats)' : '' }}</span>
              </td>
              <td>
                <form method="POST" action="{{ route('admin.resources.delete', $res) }}" onsubmit="return confirm('Delete this resource?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="cs-button cs-button-danger cs-button-sm">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Global System Notices --}}
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Broadcast Global Notice</h2>
        <p class="cs-panel-subtitle">Publish system announcements to all campus users</p>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.notifications.store') }}" class="cs-form-grid" style="margin-bottom: 1.5rem;">
      @csrf
      <div class="cs-form-group">
        <label class="cs-form-label" for="notice_title">Notice Title</label>
        <input class="cs-input" id="notice_title" name="title" placeholder="e.g. Campus Maintenance Schedule, Holiday Advisory" required>
      </div>
      <div class="cs-form-group">
        <label class="cs-form-label" for="notice_message">Notice Message</label>
        <textarea class="cs-textarea" id="notice_message" name="message" rows="4" placeholder="Write full details of the notice..." required></textarea>
      </div>
      <button type="submit" class="cs-button cs-button-success">Publish to Everyone</button>
    </form>

    <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">Recent Published Notices</h3>
    <div class="cs-notice-list">
      @forelse($notifications as $notif)
        <div class="cs-notice-card">
          <div class="cs-notice-header">
            <span class="cs-notice-title">{{ $notif->title }}</span>
            <span class="cs-notice-time">{{ $notif->created_at->diffForHumans() }}</span>
          </div>
          <div class="cs-notice-body">{{ $notif->message }}</div>
        </div>
      @empty
        <p style="color: var(--text-muted); text-align: center; padding: 1rem 0;">No notices published yet.</p>
      @endforelse
    </div>
  </div>
</div>
@endsection
