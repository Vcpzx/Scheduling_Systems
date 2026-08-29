@extends('layouts.app')

@section('title', 'Student Dashboard | Campus Scheduler')

@section('content')
<div class="cs-header-banner">
  <div>
    <h1>Student Dashboard</h1>
    <p>Welcome back, <strong>{{ $student->name }}</strong> ({{ $student->user_id }}{{ $student->section ? ' · Section '.$student->section : '' }}). View your class timetable, enrolled subjects, and announcements.</p>
  </div>
  <div>
    <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-button">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14l2 2 4-4"></path></svg>
      <span>Subject Registration</span>
    </a>
  </div>
</div>

{{-- 1. Active / Upcoming Class Notification Banner --}}
@if($currentClass)
  <div class="cs-upcoming-alert">
    <div class="cs-upcoming-content">
      <div class="cs-upcoming-icon">
        <span class="cs-status-indicator active"></span>
      </div>
      <div>
        <div class="cs-upcoming-title"><span class="cs-status-indicator active"></span> Current Class In Session (Attending Now)</div>
        <div class="cs-upcoming-subject">{{ $currentClass->subject_code }} — {{ $currentClass->subject_name }}</div>
        <div class="cs-upcoming-meta">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            {{ $currentClass->formatted_time }}
          </span>
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            {{ $currentClass->teacher->name ?? 'Instructor' }}
          </span>
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            {{ $currentClass->effective_room }}
          </span>
        </div>
      </div>
    </div>
    <div>
      <span class="cs-badge cs-badge-approved" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Happening Now</span>
    </div>
  </div>
@elseif($upcomingClass)
  <div class="cs-upcoming-alert" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
    <div class="cs-upcoming-content">
      <div class="cs-upcoming-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
      </div>
      <div>
        <div class="cs-upcoming-title">Next Class Today</div>
        <div class="cs-upcoming-subject">{{ $upcomingClass->subject_code }} — {{ $upcomingClass->subject_name }}</div>
        <div class="cs-upcoming-meta">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            Starts at {{ date('g:i A', strtotime($upcomingClass->start_time)) }} ({{ $upcomingClass->formatted_time }})
          </span>
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            {{ $upcomingClass->teacher->name ?? 'Instructor' }}
          </span>
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            {{ $upcomingClass->effective_room }}
          </span>
        </div>
      </div>
    </div>
    <div>
      <span class="cs-badge cs-badge-warning" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Upcoming Today</span>
    </div>
  </div>
@endif

{{-- Sub-Navigation Tabs --}}
<div class="cs-tabs-wrapper">
  <a href="{{ route('student.dashboard', ['tab' => 'schedule']) }}" class="cs-tab-btn {{ $tab === 'schedule' ? 'active' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
    <span>My Class Schedule</span>
    <span class="cs-tab-count">{{ $enrolledClasses->count() }}</span>
  </a>
  <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-tab-btn {{ $tab === 'registration' ? 'active' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><path d="M9 14l2 2 4-4"></path></svg>
    <span>Registration & Subjects</span>
    <span class="cs-tab-count">{{ count($availableOfferings) }}</span>
  </a>
  <a href="{{ route('student.dashboard', ['tab' => 'notifications']) }}" class="cs-tab-btn {{ $tab === 'notifications' ? 'active' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
    <span>Notifications Inbox</span>
    <span class="cs-tab-count">{{ $directNotifications->count() + $systemNotifications->count() }}</span>
  </a>
  <a href="{{ route('student.dashboard', ['tab' => 'profile']) }}" class="cs-tab-btn {{ $tab === 'profile' ? 'active' : '' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
    <span>Account Settings</span>
  </a>
</div>

{{-- TAB 1: MY SCHEDULE --}}
@if($tab === 'schedule')
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Enrolled Subjects & Room Schedules</h2>
        <p class="cs-panel-subtitle">Your personal timetable generated automatically from your enrolled classes</p>
      </div>
      <div>
        <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-button cs-button-secondary cs-button-sm">
          <span>+ Add / Change Subjects</span>
        </a>
      </div>
    </div>

    @if($enrolledClasses->isEmpty())
      <div style="text-align: center; padding: 3.5rem 1rem;">
        <div style="margin-bottom: 0.75rem; color: var(--text-light);">
          <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">No Enrolled Classes Found</h3>
        <p style="color: var(--text-muted); max-width: 450px; margin: 0.5rem auto 1.5rem;">You haven't enrolled in any subjects yet. Visit the Registration section to automatically generate your timetable by selecting your section or subjects.</p>
        <a href="{{ route('student.dashboard', ['tab' => 'registration']) }}" class="cs-button">Go to Subject Registration</a>
      </div>
    @else
      <div class="schedule-grid">
        @foreach($enrolledClasses as $cs)
          @php $isActive = $cs->isCurrentClass(); @endphp
          <div class="schedule-card {{ $isActive ? 'active-now' : '' }}">
            <div>
              <div class="schedule-card-header">
                <div>
                  <span class="schedule-subject-code">{{ $cs->subject_code }}</span>
                  <span class="schedule-section-badge">{{ $cs->section }}</span>
                </div>
                @if($isActive)
                  <span class="cs-badge cs-badge-approved">
                    <span class="cs-status-indicator active"></span> Current Class
                  </span>
                @elseif($cs->isUpcomingToday())
                  <span class="cs-badge cs-badge-warning">Today</span>
                @endif
              </div>

              <h3 class="schedule-subject-name">{{ $cs->subject_name }}</h3>

              <div style="margin-bottom: 1rem;">
                <div style="font-weight: 700; color: var(--text-main); font-size: 0.95rem;">{{ $cs->formatted_time }}</div>
                <div class="schedule-days-text">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                  {{ $cs->days }} · {{ $cs->semester }}
                </div>
              </div>
            </div>

            <div class="schedule-meta-list">
              <div class="schedule-meta-item">
                <span class="schedule-meta-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </span>
                <span><strong>Instructor:</strong> {{ $cs->teacher->name ?? 'Prof. Assigned' }}</span>
              </div>
              <div class="schedule-meta-item">
                <span class="schedule-meta-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </span>
                <span><strong>Room / Location:</strong> {{ $cs->effective_room }}</span>
              </div>
              @if($cs->notes)
                <div class="schedule-meta-item" style="color: var(--text-muted); font-size: 0.8rem;">
                  <span class="schedule-meta-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                  </span>
                  <span>{{ $cs->notes }}</span>
                </div>
              @endif
            </div>

            <div style="display: flex; justify-content: flex-end; padding-top: 0.75rem; border-top: 1px solid var(--border);">
              <form method="POST" action="{{ route('student.unenroll', $cs) }}" onsubmit="return confirm('Drop {{ $cs->subject_code }} from your schedule?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cs-button cs-button-secondary cs-button-sm" style="color: var(--danger);">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                  <span>Drop Subject</span>
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

{{-- TAB 2: REGISTRATION SECTION --}}
@elseif($tab === 'registration')
  <div class="cs-grid-2" style="align-items: start;">
    {{-- Section Quick-Enrollment --}}
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">1-Click Section Registration</h2>
          <p class="cs-panel-subtitle">Automatically load all subject schedules for your assigned class section</p>
        </div>
      </div>

      <form method="POST" action="{{ route('student.enroll.section') }}" class="cs-form-grid">
        @csrf

        <div class="cs-form-group">
          <label class="cs-form-label" for="reg_section">Select Your Class Section</label>
          <select class="cs-select" id="reg_section" name="section" required>
            <option value="">Choose your class section</option>
            @foreach($availableSections as $sec)
              <option value="{{ $sec }}" {{ $student->section === $sec ? 'selected' : '' }}>{{ $sec }}</option>
            @endforeach
          </select>
        </div>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="reg_semester">Semester</label>
            <select class="cs-select" id="reg_semester" name="semester">
              <option value="1st Semester">1st Semester</option>
              <option value="2nd Semester">2nd Semester</option>
              <option value="Summer">Summer Term</option>
              <option value="all">All Semesters</option>
            </select>
          </div>
          <div class="cs-form-group">
            <label class="cs-form-label" for="reg_year">Academic Year</label>
            <input class="cs-input" id="reg_year" name="academic_year" value="2026-2027" required>
          </div>
        </div>

        <button type="submit" class="cs-button cs-button-success" style="width: 100%;">
          <span>Auto-Generate My Schedule</span>
        </button>
      </form>
    </div>

    {{-- Individual Subject Enrollment --}}
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">Available Semester Offerings</h2>
          <p class="cs-panel-subtitle">Enroll in individual subjects or electives</p>
        </div>
      </div>

      @if($availableOfferings->isEmpty())
        <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No active subject offerings available for this term.</p>
      @else
        <div class="cs-table-container">
          <table class="cs-table">
            <thead>
              <tr>
                <th>Subject & Section</th>
                <th>Schedule & Room</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($availableOfferings as $offering)
                @php $isEnrolled = in_array($offering->id, $enrolledScheduleIds); @endphp
                <tr>
                  <td>
                    <strong>{{ $offering->subject_code }}</strong> <span class="cs-badge cs-badge-neutral">{{ $offering->section }}</span>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $offering->subject_name }}</div>
                    <div style="font-size: 0.75rem; color: var(--primary); display: flex; align-items: center; gap: 0.35rem; margin-top: 0.15rem;">
                      <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                      {{ $offering->teacher->name ?? 'Instructor' }}
                    </div>
                  </td>
                  <td>
                    <div><strong>{{ $offering->formatted_time }}</strong></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $offering->days }} · {{ $offering->effective_room }}</div>
                  </td>
                  <td>
                    @if($isEnrolled)
                      <span class="cs-badge cs-badge-approved">Enrolled</span>
                    @else
                      <form method="POST" action="{{ route('student.enroll.subject') }}">
                        @csrf
                        <input type="hidden" name="class_schedule_id" value="{{ $offering->id }}">
                        <button type="submit" class="cs-button cs-button-sm">Enroll</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

{{-- TAB 3: NOTIFICATIONS INBOX --}}
@elseif($tab === 'notifications')
  <div class="cs-grid-2" style="align-items: start;">
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">Teacher Announcements</h2>
          <p class="cs-panel-subtitle">Notices for your section ({{ $student->section ?? 'General' }}) or sent directly to you</p>
        </div>
      </div>

      <div class="cs-notice-list">
        @forelse($directNotifications as $notice)
          <div class="cs-notice-card">
            <div class="cs-notice-header">
              <span class="cs-notice-title">{{ $notice->title }}</span>
              <span class="cs-notice-time">{{ $notice->created_at->diffForHumans() }}</span>
            </div>
            <div class="cs-notice-body">{{ $notice->message }}</div>
            <div class="cs-notice-sender">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              From: {{ $notice->sender->name ?? 'Instructor' }}
              @if($notice->recipient_type === 'section')
                · (To Section {{ $notice->target_section }})
              @elseif($notice->recipient_type === 'student')
                · (Direct Message)
              @endif
            </div>
          </div>
        @empty
          <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No teacher announcements received yet.</p>
        @endforelse
      </div>
    </div>

    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title">Campus-Wide Notices</h2>
          <p class="cs-panel-subtitle">Official announcements from the administration</p>
        </div>
      </div>

      <div class="cs-notice-list">
        @forelse($systemNotifications as $notice)
          <div class="cs-notice-card">
            <div class="cs-notice-header">
              <span class="cs-notice-title">{{ $notice->title }}</span>
              <span class="cs-notice-time">{{ $notice->created_at->diffForHumans() }}</span>
            </div>
            <div class="cs-notice-body">{{ $notice->message }}</div>
            <div class="cs-notice-sender">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
              Campus Administration
            </div>
          </div>
        @empty
          <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No system notices at this time.</p>
        @endforelse
      </div>
    </div>
  </div>

{{-- TAB 4: MASTER CAMPUS VIEW --}}
@elseif($tab === 'campus-schedule')
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Campus Master Schedule & Room Occupancy</h2>
        <p class="cs-panel-subtitle">Visual timeline of room and facility reservations</p>
      </div>
    </div>

    <form method="GET" action="{{ route('student.dashboard') }}" class="cs-controls" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
      <input type="hidden" name="tab" value="campus-schedule">
      <input type="date" name="date" class="cs-input" style="width: auto;" value="{{ $scheduleDate }}" onchange="this.form.submit()">
      <div class="cs-filter-group">
        @foreach(['all' => 'All Resources', 'room' => 'Classrooms', 'lab' => 'Laboratories', 'facility' => 'Facilities', 'equipment' => 'Equipment'] as $key => $label)
          <button type="submit" name="type" value="{{ $key }}" class="cs-filter-btn {{ $scheduleType === $key ? 'active' : '' }}">{{ $label }}</button>
        @endforeach
      </div>
    </form>

    <div class="cs-grid-wrap">
      <div class="cs-grid-inner">
        <div class="cs-grid-header-row">
          <div class="cs-grid-label-col">Resource / Room</div>
          <div class="cs-grid-hours">
            @for($m = 420; $m <= 1260; $m += 60)
              <div class="cs-hour-tick">{{ date('g:i A', mktime(0, $m)) }}</div>
            @endfor
          </div>
        </div>

        @forelse($scheduleResources as $resource)
          <div class="cs-resource-row">
            <div class="cs-resource-label">
              <div style="font-weight: 700;">{{ $resource->name }}</div>
              <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $resource->location }} {{ $resource->capacity ? '· '.$resource->capacity.' seats' : '' }}</div>
              <span class="cs-badge cs-badge-neutral" style="margin-top: 0.25rem;">{{ strtoupper($resource->type) }}</span>
            </div>
            <div class="cs-track" style="width: 1120px;">
              @foreach($campusBookings->where('resource_id', $resource->id) as $booking)
                @php 
                  $startMin = (strtotime($booking->start_time) - strtotime('07:00')) / 60; 
                  $duration = (strtotime($booking->end_time) - strtotime($booking->start_time)) / 60; 
                @endphp
                <div class="cs-booking-block {{ $booking->status }}" style="left: {{ $startMin * (80 / 60) }}px; width: {{ max($duration * (80 / 60), 30) }}px;" title="{{ $booking->purpose }}">
                  <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $booking->purpose }}</div>
                  <div style="font-size: 0.7rem; opacity: 0.9;">{{ $booking->user?->name }} · {{ date('g:i A', strtotime($booking->start_time)) }}</div>
                </div>
              @endforeach
            </div>
          </div>
        @empty
          <div style="padding: 2rem; text-align: center; color: var(--text-muted);">No resources found.</div>
        @endforelse
      </div>
    </div>
  </div>

{{-- TAB 5: ACCOUNT & CREDENTIALS --}}
@elseif($tab === 'profile')
  <div class="cs-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title">Account Settings</h2>
        <p class="cs-panel-subtitle">Update your student information, class section, email, and password</p>
      </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="cs-form-grid">
      @csrf
      @method('PUT')

      <div class="cs-form-group">
        <label class="cs-form-label" for="stu_name">Full Name</label>
        <input class="cs-input" id="stu_name" name="name" value="{{ old('name', $student->name) }}" required>
      </div>

      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="stu_user_id">Student ID</label>
          <input class="cs-input" id="stu_user_id" name="user_id" value="{{ old('user_id', $student->user_id) }}" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="stu_section">Class Section</label>
          <input class="cs-input" id="stu_section" name="section" value="{{ old('section', $student->section) }}" placeholder="e.g. BSIS 3A">
        </div>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="stu_email">Email Address</label>
        <input class="cs-input" type="email" id="stu_email" name="email" value="{{ old('email', $student->email) }}" placeholder="student@campus.edu">
      </div>

      <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: 0.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;">Change Password</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Leave blank if you do not wish to change your password.</p>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="stu_password">New Password</label>
            <div class="cs-password-wrapper">
              <input class="cs-input" type="password" id="stu_password" name="password" minlength="6" placeholder="New password">
              <button type="button" class="cs-password-toggle" title="Toggle password visibility">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>

          <div class="cs-form-group">
            <label class="cs-form-label" for="stu_password_confirmation">Confirm Password</label>
            <div class="cs-password-wrapper">
              <input class="cs-input" type="password" id="stu_password_confirmation" name="password_confirmation" minlength="6" placeholder="Confirm password">
              <button type="button" class="cs-password-toggle" title="Toggle password visibility">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <button type="submit" class="cs-button cs-button-success" style="width: 100%; margin-top: 0.5rem;">
        <span>Save Profile & Credentials</span>
      </button>
    </form>
  </div>
@endif

@endsection