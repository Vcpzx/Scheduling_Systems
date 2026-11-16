@extends('layouts.app')

@php
  $sectionTitles = [
    'dashboard' => 'Admin Dashboard',
    'accounts' => 'Accounts Awaiting Review',
    'bookings' => 'Space Booking Requests',
    'resources' => 'Campus Resources Management',
    'notices' => 'Broadcast Global Notice',
    'terms' => 'Academic Term Dates',
  ];
@endphp

@section('title', $sectionTitles[$section].' | Campus Scheduler')

@section('content')
<div class="cs-header-banner">
  <div>
    <h1>{{ $sectionTitles[$section] }}</h1>
    <p>{{ $section === 'dashboard' ? 'Campus overview · '.($activeTerm ? $activeTerm->semester.' · '.$activeTerm->academic_year : 'No active academic term') : 'Admin Workspace' }}</p>
  </div>
  <div>
    <span class="cs-badge cs-badge-approved" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Admin Mode Active</span>
  </div>
</div>

@if($section === 'dashboard')
  <div class="cs-admin-stat-grid">
    <a class="cs-admin-stat-card" href="{{ route('admin.dashboard', ['section' => 'accounts']) }}">
      <span class="cs-admin-stat-label">Pending Accounts</span>
      <strong>{{ $dashboardStats['pending_accounts'] }}</strong>
      <span class="cs-admin-stat-hint">Review registration requests</span>
    </a>
    <div class="cs-admin-stat-card">
      <span class="cs-admin-stat-label">Approved Students</span>
      <strong>{{ $dashboardStats['students'] }}</strong>
      <span class="cs-admin-stat-hint">Active student accounts</span>
    </div>
    <div class="cs-admin-stat-card">
      <span class="cs-admin-stat-label">Approved Teachers</span>
      <strong>{{ $dashboardStats['teachers'] }}</strong>
      <span class="cs-admin-stat-hint">Active faculty accounts</span>
    </div>
    <a class="cs-admin-stat-card" href="{{ route('schedule.index') }}">
      <span class="cs-admin-stat-label">Classes This Term</span>
      <strong>{{ $dashboardStats['active_classes'] }}</strong>
      <span class="cs-admin-stat-hint">{{ $dashboardStats['active_enrollments'] }} student enrollments</span>
    </a>
    <a class="cs-admin-stat-card" href="{{ route('admin.dashboard', ['section' => 'bookings']) }}">
      <span class="cs-admin-stat-label">Pending Room Requests</span>
      <strong>{{ $dashboardStats['pending_bookings'] }}</strong>
      <span class="cs-admin-stat-hint">Review room requests</span>
    </a>
    <a class="cs-admin-stat-card" href="{{ route('admin.dashboard', ['section' => 'resources']) }}">
      <span class="cs-admin-stat-label">Classrooms & Labs</span>
      <strong>{{ $dashboardStats['rooms'] }}</strong>
      <span class="cs-admin-stat-hint">Managed campus spaces</span>
    </a>
  </div>

  <div class="cs-grid-2 cs-admin-dashboard-charts">
    <section class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">Weekly Class Distribution</h2>
          <p class="cs-panel-subtitle">Scheduled classes by weekday for the active term</p>
        </div>
      </div>
      <div class="cs-admin-bar-chart" role="img" aria-label="Weekly class distribution chart">
        @foreach($weekdayScheduleCounts as $day => $count)
          @php $barHeight = $count ? max(8, round(($count / $maxWeekdayScheduleCount) * 100)) : 0; @endphp
          <div class="cs-admin-bar-column" aria-label="{{ $day }}: {{ $count }} classes">
            <strong>{{ $count }}</strong>
            <div class="cs-admin-bar-track">
              <div class="cs-admin-bar-fill" style="height: {{ $barHeight }}%;"></div>
            </div>
            <span>{{ substr($day, 0, 3) }}</span>
          </div>
        @endforeach
      </div>
    </section>

    <section class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">Room Request Status</h2>
          <p class="cs-panel-subtitle">All room requests by approval status</p>
        </div>
      </div>
      @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $status => $label)
        @php
          $count = $bookingStatusCounts->get($status, 0);
          $barWidth = $count ? max(3, round(($count / $maxBookingStatusCount) * 100)) : 0;
        @endphp
        <div class="cs-admin-status-row">
          <div class="cs-admin-status-label">
            <span>{{ $label }}</span>
            <strong>{{ $count }}</strong>
          </div>
          <div class="cs-admin-status-track">
            <div class="cs-admin-status-fill {{ $status }}" style="width: {{ $barWidth }}%;"></div>
          </div>
        </div>
      @endforeach
    </section>
  </div>

  <section class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Recent Room Requests</h2>
        <p class="cs-panel-subtitle">Latest booking activity across campus</p>
      </div>
      <a class="cs-button cs-button-secondary cs-button-sm" href="{{ route('admin.dashboard', ['section' => 'bookings']) }}">Review Requests</a>
    </div>
    @if($recentBookings->isEmpty())
      <p class="cs-calendar-empty">No room requests have been submitted yet.</p>
    @else
      <div class="cs-table-container">
        <table class="cs-table cs-admin-table">
          <thead>
            <tr>
              <th>Room</th>
              <th>Requested By</th>
              <th>Date & Time</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($recentBookings as $booking)
              <tr>
                <td><strong>{{ $booking->resource->name }}</strong></td>
                <td>{{ $booking->user->name }}</td>
                <td>
                  {{ date('M j, Y', strtotime($booking->date)) }}
                  <div class="cs-panel-subtitle">{{ date('g:i A', strtotime($booking->start_time)) }}–{{ date('g:i A', strtotime($booking->end_time)) }}</div>
                </td>
                <td><span class="cs-badge cs-badge-{{ $booking->status === 'approved' ? 'approved' : ($booking->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($booking->status) }}</span></td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>
@endif

@if($section === 'accounts')
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
        <table class="cs-table cs-admin-table">
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
                  <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $user->user_id }}{{ $user->course_section ? ' · '.$user->course_section : '' }}</div>
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
@endif

@if($section === 'bookings')
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
        <table class="cs-table cs-admin-table">
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
@endif

@if($section === 'resources')
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
      <table class="cs-table cs-admin-table">
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
@endif

@if($section === 'notices')
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
@endif

@if($section === 'terms')
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Configure Academic Terms</h2>
        <p class="cs-panel-subtitle">Set the date ranges used to switch everyone's schedule automatically.</p>
      </div>
    </div>

    <form method="POST" action="{{ route('admin.academic-terms.store') }}" class="cs-form-grid" style="margin-bottom: 1.5rem;">
      @csrf
      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="term_semester">Semester</label>
          <select class="cs-select" id="term_semester" name="semester" required>
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
            <option value="Summer">Summer</option>
          </select>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="term_year">Academic Year</label>
          <input class="cs-input" id="term_year" name="academic_year" placeholder="e.g. 2026-2027" required>
        </div>
      </div>
      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="term_starts_on">Starts on</label>
          <input class="cs-input" type="date" id="term_starts_on" name="starts_on" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="term_ends_on">Ends on</label>
          <input class="cs-input" type="date" id="term_ends_on" name="ends_on" required>
        </div>
      </div>
      <button type="submit" class="cs-button">Add Academic Term</button>
    </form>

    @if($academicTerms->isEmpty())
      <p class="cs-calendar-empty">No term dates configured yet. The calendar currently uses the most recently added class schedule until dates are entered.</p>
    @else
      <div class="cs-table-container">
        <table class="cs-table cs-admin-table">
          <thead>
            <tr>
              <th>Semester</th>
              <th>Academic Year</th>
              <th>Starts</th>
              <th>Ends</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($academicTerms as $term)
              <tr>
                <td colspan="5">
                  <form method="POST" action="{{ route('admin.academic-terms.update', $term) }}" class="cs-term-edit-row">
                    @csrf
                    @method('PUT')
                    <select class="cs-select" name="semester" aria-label="Semester" required>
                      @foreach(['1st Semester', '2nd Semester', 'Summer'] as $semester)
                        <option value="{{ $semester }}" {{ $term->semester === $semester ? 'selected' : '' }}>{{ $semester }}</option>
                      @endforeach
                    </select>
                    <input class="cs-input" name="academic_year" aria-label="Academic year" value="{{ $term->academic_year }}" required>
                    <input class="cs-input" type="date" name="starts_on" aria-label="Starts on" value="{{ $term->starts_on->format('Y-m-d') }}" required>
                    <input class="cs-input" type="date" name="ends_on" aria-label="Ends on" value="{{ $term->ends_on->format('Y-m-d') }}" required>
                    <button class="cs-button cs-button-sm" type="submit">Save</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <p class="cs-panel-subtitle" style="margin-top: 1rem;">The term that includes today is active. When it ends, the next scheduled term becomes active automatically.</p>
    @endif
  </div>
@endif
@endsection
