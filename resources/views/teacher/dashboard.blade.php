@extends('layouts.app')

@section('title', 'Teacher Dashboard | Campus Scheduler')

@section('content')
<div class="cs-header-banner">
  <div>
    <h1>Teacher Dashboard</h1>
    <p>Welcome back, <strong>{{ $teacher->name }}</strong>. Manage your semester loads, room bookings, and student communications.</p>
  </div>
  <div>
    <a href="{{ route('teacher.dashboard', ['tab' => 'semesters']) }}" class="cs-button cs-button-primary">
      <span>➕ New Class Schedule</span>
    </a>
  </div>
</div>

{{-- 1. Upcoming Class Notifications Banner --}}
@if($currentClass)
  <div class="cs-upcoming-alert">
    <div class="cs-upcoming-content">
      <div class="cs-upcoming-icon">
        <span class="cs-pulse-dot"></span>
      </div>
      <div>
        <div class="cs-upcoming-title">🔴 Class In Session Right Now</div>
        <div class="cs-upcoming-subject">{{ $currentClass->subject_code }} — {{ $currentClass->subject_name }} ({{ $currentClass->section }})</div>
        <div class="cs-upcoming-meta">
          <span>🕒 {{ $currentClass->formatted_time }}</span>
          <span>📍 {{ $currentClass->effective_room }}</span>
          <span>👥 {{ $currentClass->students->count() }} enrolled students</span>
        </div>
      </div>
    </div>
    <div>
      <span class="cs-badge cs-badge-approved" style="font-size: 0.85rem; padding: 0.5rem 1rem;">Active Now</span>
    </div>
  </div>
@elseif($upcomingClass)
  <div class="cs-upcoming-alert" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
    <div class="cs-upcoming-content">
      <div class="cs-upcoming-icon">
        <span>⏰</span>
      </div>
      <div>
        <div class="cs-upcoming-title">Upcoming Class Today</div>
        <div class="cs-upcoming-subject">{{ $upcomingClass->subject_code }} — {{ $upcomingClass->subject_name }} ({{ $upcomingClass->section }})</div>
        <div class="cs-upcoming-meta">
          <span>🕒 Starts at {{ date('g:i A', strtotime($upcomingClass->start_time)) }} ({{ $upcomingClass->formatted_time }})</span>
          <span>📍 {{ $upcomingClass->effective_room }}</span>
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
  <a href="{{ route('teacher.dashboard', ['tab' => 'schedule']) }}" class="cs-tab-btn {{ $tab === 'schedule' ? 'active' : '' }}">
    <span>📅 Class Timetable & Schedule</span>
    <span class="cs-tab-count">{{ $classSchedules->count() }}</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'semesters']) }}" class="cs-tab-btn {{ $tab === 'semesters' ? 'active' : '' }}">
    <span>📚 Semester Handling & Loads</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'room-requests']) }}" class="cs-tab-btn {{ $tab === 'room-requests' ? 'active' : '' }}">
    <span>🚪 Room Requests</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'recent-requests']) }}" class="cs-tab-btn {{ $tab === 'recent-requests' ? 'active' : '' }}">
    <span>📋 Recent Requests</span>
    <span class="cs-tab-count">{{ $myBookings->count() }}</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'send-notification']) }}" class="cs-tab-btn {{ $tab === 'send-notification' ? 'active' : '' }}">
    <span>✉️ Notify Students</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'notifications']) }}" class="cs-tab-btn {{ $tab === 'notifications' ? 'active' : '' }}">
    <span>🔔 Inbox & Notices</span>
    <span class="cs-tab-count">{{ $systemNotifications->count() + $sentNotifications->count() }}</span>
  </a>
  <a href="{{ route('teacher.dashboard', ['tab' => 'profile']) }}" class="cs-tab-btn {{ $tab === 'profile' ? 'active' : '' }}">
    <span>👤 Account & Credentials</span>
  </a>
</div>

{{-- TAB 1: SCHEDULE CARDS & TIMETABLE --}}
@if($tab === 'schedule')
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title"><span>📅</span> My Teaching Schedule</h2>
        <p class="cs-panel-subtitle">Classes assigned for {{ $selectedSemester }} (A.Y. {{ $selectedYear }})</p>
      </div>
      <form method="GET" action="{{ route('teacher.dashboard') }}" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <input type="hidden" name="tab" value="schedule">
        <select name="semester" class="cs-select" style="width: auto;" onchange="this.form.submit()">
          <option value="1st Semester" {{ $selectedSemester === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
          <option value="2nd Semester" {{ $selectedSemester === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
          <option value="Summer" {{ $selectedSemester === 'Summer' ? 'selected' : '' }}>Summer</option>
          <option value="all" {{ $selectedSemester === 'all' ? 'selected' : '' }}>All Semesters</option>
        </select>
        <select name="academic_year" class="cs-select" style="width: auto;" onchange="this.form.submit()">
          <option value="2026-2027" {{ $selectedYear === '2026-2027' ? 'selected' : '' }}>2026-2027</option>
          <option value="2025-2026" {{ $selectedYear === '2025-2026' ? 'selected' : '' }}>2025-2026</option>
          <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>All Years</option>
        </select>
      </form>
    </div>

    @if($classSchedules->isEmpty())
      <div style="text-align: center; padding: 3rem 1rem;">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📚</div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">No Teaching Loads Found</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't registered any class schedules for this semester yet.</p>
        <a href="{{ route('teacher.dashboard', ['tab' => 'semesters']) }}" class="cs-button">Create Class Schedule</a>
      </div>
    @else
      <div class="schedule-grid">
        @foreach($classSchedules as $cs)
          @php $isActive = $cs->isCurrentClass(); @endphp
          <div class="schedule-card {{ $isActive ? 'current-class' : '' }}">
            <div>
              <div class="schedule-card-header">
                <div>
                  <span class="schedule-code-badge">{{ $cs->subject_code }}</span>
                  <span class="schedule-section-badge">{{ $cs->section }}</span>
                </div>
                @if($isActive)
                  <span class="schedule-live-badge">
                    <span class="cs-pulse-dot"></span> Current Class
                  </span>
                @elseif($cs->isUpcomingToday())
                  <span class="schedule-upcoming-badge">⏳ Today</span>
                @endif
              </div>

              <h3 class="schedule-subject-name">{{ $cs->subject_name }}</h3>

              <div class="schedule-time-box">
                <div class="schedule-time-icon">🕒</div>
                <div>
                  <div class="schedule-time-text">{{ $cs->formatted_time }}</div>
                  <div class="schedule-days-text">📅 {{ $cs->days }} · {{ $cs->semester }}</div>
                </div>
              </div>
            </div>

            <div class="schedule-meta-list">
              <div class="schedule-meta-item">
                <span>📍</span>
                <span><strong>Room:</strong> {{ $cs->effective_room }}</span>
              </div>
              <div class="schedule-meta-item">
                <span>👥</span>
                <span><strong>Students:</strong> {{ $cs->students->count() }} enrolled</span>
              </div>
              @if($cs->notes)
                <div class="schedule-meta-item" style="color: var(--text-muted); font-size: 0.8rem;">
                  <span>📝</span>
                  <span>{{ $cs->notes }}</span>
                </div>
              @endif
            </div>

            <div style="display: flex; justify-content: flex-end; padding-top: 0.75rem; border-top: 1px solid var(--border);">
              <form method="POST" action="{{ route('teacher.schedules.delete', $cs) }}" onsubmit="return confirm('Are you sure you want to remove this class schedule?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="cs-button cs-button-secondary cs-button-sm" style="color: var(--danger);">
                  🗑️ Remove
                </button>
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

{{-- TAB 2: SEMESTER HANDLING & SCHEDULE CREATION --}}
@elseif($tab === 'semesters')
  <div class="cs-grid-2" style="align-items: start;">
    {{-- Create Class Schedule & Booking Form --}}
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title"><span>➕</span> Schedule / Booking Creation</h2>
          <p class="cs-panel-subtitle">Create a class schedule or book a room for a specific section</p>
        </div>
      </div>

      <form method="POST" action="{{ route('teacher.schedules.store') }}" class="cs-form-grid">
        @csrf
        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="subject_code">Subject Code</label>
            <input class="cs-input" id="subject_code" name="subject_code" placeholder="e.g. IT301, CS102" required>
          </div>
          <div class="cs-form-group">
            <label class="cs-form-label" for="section">Section</label>
            <input class="cs-input" id="section" name="section" placeholder="e.g. BSIS 3A, BSCS 2B" required>
          </div>
        </div>

        <div class="cs-form-group">
          <label class="cs-form-label" for="subject_name">Subject Name / Course Title</label>
          <input class="cs-input" id="subject_name" name="subject_name" placeholder="e.g. Web Systems & Technologies" required>
        </div>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="semester">Semester</label>
            <select class="cs-select" id="semester" name="semester" required>
              <option value="1st Semester">1st Semester</option>
              <option value="2nd Semester">2nd Semester</option>
              <option value="Summer">Summer Term</option>
            </select>
          </div>
          <div class="cs-form-group">
            <label class="cs-form-label" for="academic_year">Academic Year</label>
            <input class="cs-input" id="academic_year" name="academic_year" value="2026-2027" required>
          </div>
        </div>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="resource_id">Assigned Room / Facility</label>
            <select class="cs-select" id="resource_id" name="resource_id">
              <option value="">Choose a campus room/lab</option>
              @foreach($resources as $res)
                <option value="{{ $res->id }}">{{ $res->name }} ({{ strtoupper($res->type) }}{{ $res->capacity ? ', '.$res->capacity.' seats' : '' }})</option>
              @endforeach
            </select>
          </div>
          <div class="cs-form-group">
            <label class="cs-form-label" for="days">Days of Week</label>
            <select class="cs-select" id="days" name="days" required>
              <option value="MWF (Monday, Wednesday, Friday)">MWF (Monday, Wednesday, Friday)</option>
              <option value="TTH (Tuesday, Thursday)">TTH (Tuesday, Thursday)</option>
              <option value="Monday">Monday Only</option>
              <option value="Tuesday">Tuesday Only</option>
              <option value="Wednesday">Wednesday Only</option>
              <option value="Thursday">Thursday Only</option>
              <option value="Friday">Friday Only</option>
              <option value="Saturday">Saturday</option>
            </select>
          </div>
        </div>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="start_time">Start Time</label>
            <input class="cs-input" type="time" id="start_time" name="start_time" value="09:00" required>
          </div>
          <div class="cs-form-group">
            <label class="cs-form-label" for="end_time">End Time</label>
            <input class="cs-input" type="time" id="end_time" name="end_time" value="10:30" required>
          </div>
        </div>

        <div class="cs-form-group">
          <label class="cs-form-label" for="notes">Notes / Class Instructions (Optional)</label>
          <textarea class="cs-textarea" id="notes" name="notes" placeholder="e.g. Bring laboratory manuals on Thursdays." rows="2"></textarea>
        </div>

        <button type="submit" class="cs-button" style="width: 100%;">
          <span>💾 Save Class Schedule</span>
        </button>
      </form>
    </div>

    {{-- Semester Handling Overview --}}
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title"><span>📚</span> Semester Handling Overview</h2>
          <p class="cs-panel-subtitle">Classes & sections you handle this academic year</p>
        </div>
      </div>

      @if($classSchedules->isEmpty())
        <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No active classes found for this semester.</p>
      @else
        <div class="cs-table-container">
          <table class="cs-table">
            <thead>
              <tr>
                <th>Subject & Section</th>
                <th>Schedule & Room</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($classSchedules as $cs)
                <tr>
                  <td>
                    <strong>{{ $cs->subject_code }}</strong> · <span class="cs-badge cs-badge-neutral">{{ $cs->section }}</span>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $cs->subject_name }}</div>
                  </td>
                  <td>
                    <div><strong>{{ $cs->formatted_time }}</strong></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $cs->days }} · {{ $cs->effective_room }}</div>
                  </td>
                  <td>
                    <form method="POST" action="{{ route('teacher.schedules.delete', $cs) }}" onsubmit="return confirm('Remove this schedule?');">
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
      @endif
    </div>
  </div>

{{-- TAB 3: ROOM REQUESTS --}}
@elseif($tab === 'room-requests')
  <div class="cs-panel" style="max-width: 760px; margin: 0 auto;">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title"><span>🚪</span> Submit Room Booking Request</h2>
        <p class="cs-panel-subtitle">Request specialized campus rooms, labs, or equipment for events, exams, or makeup classes</p>
      </div>
    </div>

    <form method="POST" action="{{ route('bookings.store') }}" class="cs-form-grid">
      @csrf
      <div class="cs-form-group">
        <label class="cs-form-label" for="resource_id">Select Resource / Room</label>
        <select class="cs-select" id="resource_id" name="resource_id" required>
          <option value="">Choose a campus room, lab, or facility</option>
          @foreach($resources as $resource)
            <option value="{{ $resource->id }}">{{ $resource->name }} ({{ strtoupper($resource->type) }}{{ $resource->location ? ' · '.$resource->location : '' }}{{ $resource->capacity ? ' · '.$resource->capacity.' seats' : '' }})</option>
          @endforeach
        </select>
      </div>

      <div class="cs-grid-3">
        <div class="cs-form-group">
          <label class="cs-form-label" for="date">Booking Date</label>
          <input class="cs-input" type="date" id="date" name="date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="req_start_time">Start Time</label>
          <input class="cs-input" type="time" id="req_start_time" name="start_time" value="09:00" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="req_end_time">End Time</label>
          <input class="cs-input" type="time" id="req_end_time" name="end_time" value="11:00" required>
        </div>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="purpose">Purpose / Details of Request</label>
        <textarea class="cs-textarea" id="purpose" name="purpose" rows="4" placeholder="e.g. Midterm Examination for BSIS 3A students, projector required." required></textarea>
      </div>

      <button type="submit" class="cs-button" style="width: 100%;">
        <span>📤 Submit Booking Request for Admin Approval</span>
      </button>
    </form>
  </div>

{{-- TAB 4: RECENT REQUESTS --}}
@elseif($tab === 'recent-requests')
  <div class="cs-panel">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title"><span>📋</span> Room Request History & Status</h2>
        <p class="cs-panel-subtitle">Track the approval status of your past and pending room reservation requests</p>
      </div>
      <div>
        <a href="{{ route('teacher.dashboard', ['tab' => 'room-requests']) }}" class="cs-button cs-button-sm">
          <span>➕ New Request</span>
        </a>
      </div>
    </div>

    @if($myBookings->isEmpty())
      <div style="text-align: center; padding: 3rem 1rem;">
        <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">🚪</div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-main);">No Room Requests Yet</h3>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't submitted any room booking requests.</p>
        <a href="{{ route('teacher.dashboard', ['tab' => 'room-requests']) }}" class="cs-button">Submit a Request</a>
      </div>
    @else
      <div class="cs-table-container">
        <table class="cs-table">
          <thead>
            <tr>
              <th>Resource / Room</th>
              <th>Date & Time</th>
              <th>Purpose</th>
              <th>Status</th>
              <th>Requested On</th>
            </tr>
          </thead>
          <tbody>
            @foreach($myBookings as $booking)
              <tr>
                <td>
                  <strong>{{ $booking->resource->name }}</strong>
                  <div style="font-size: 0.8rem; color: var(--text-muted);">{{ strtoupper($booking->resource->type) }} · {{ $booking->resource->location }}</div>
                </td>
                <td>
                  <div><strong>{{ date('M d, Y', strtotime($booking->date)) }}</strong></div>
                  <div style="font-size: 0.85rem; color: var(--text-muted);">{{ date('g:i A', strtotime($booking->start_time)) }} - {{ date('g:i A', strtotime($booking->end_time)) }}</div>
                </td>
                <td>{{ $booking->purpose }}</td>
                <td>
                  @if($booking->status === 'approved')
                    <span class="cs-badge cs-badge-approved">✅ Approved</span>
                  @elseif($booking->status === 'pending')
                    <span class="cs-badge cs-badge-pending">⏳ Pending Review</span>
                  @else
                    <span class="cs-badge cs-badge-rejected">❌ Declined</span>
                  @endif
                </td>
                <td style="font-size: 0.8rem; color: var(--text-muted);">
                  {{ $booking->created_at->format('M d, Y g:i A') }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

{{-- TAB 5: SEND NOTIFICATION --}}
@elseif($tab === 'send-notification')
  <div class="cs-panel" style="max-width: 760px; margin: 0 auto;">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title"><span>✉️</span> Send Notification to Students</h2>
        <p class="cs-panel-subtitle">Compose and send class announcements or individual reminders</p>
      </div>
    </div>

    <form method="POST" action="{{ route('teacher.notifications.send') }}" class="cs-form-grid">
      @csrf

      <div class="cs-form-group">
        <label class="cs-form-label" for="recipient_type">Recipient Scope</label>
        <select class="cs-select" id="recipient_type" name="recipient_type" required onchange="handleRecipientChange(this.value)">
          <option value="section">All Students in a Class Section</option>
          <option value="student">A Specific Student</option>
          <option value="all">All Campus Students (General Notice)</option>
        </select>
      </div>

      <div class="cs-form-group" id="section-select-group">
        <label class="cs-form-label" for="target_section">Target Section</label>
        <select class="cs-select" id="target_section" name="target_section">
          @if($sectionsHandled->isEmpty())
            <option value="BSIS 3A">BSIS 3A</option>
            <option value="BSCS 2B">BSCS 2B</option>
          @else
            @foreach($sectionsHandled as $sec)
              <option value="{{ $sec }}">{{ $sec }}</option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="cs-form-group" id="student-select-group" style="display: none;">
        <label class="cs-form-label" for="target_user_id">Select Specific Student</label>
        <select class="cs-select" id="target_user_id" name="target_user_id">
          <option value="">Choose a student</option>
          @foreach($studentsInMySections as $stu)
            <option value="{{ $stu->id }}">{{ $stu->name }} ({{ $stu->user_id }}{{ $stu->section ? ' · '.$stu->section : '' }})</option>
          @endforeach
        </select>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="title">Notification Title / Subject</label>
        <input class="cs-input" id="title" name="title" placeholder="e.g. Room Change for Wednesday Lecture, Exam Review Materials" required>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="message">Message Body</label>
        <textarea class="cs-textarea" id="message" name="message" rows="5" placeholder="Write your announcement or reminder here..." required></textarea>
      </div>

      <button type="submit" class="cs-button" style="width: 100%;">
        <span>📢 Broadcast Notification</span>
      </button>
    </form>
  </div>

  <script>
    function handleRecipientChange(val) {
      const secGroup = document.getElementById('section-select-group');
      const stuGroup = document.getElementById('student-select-group');
      if (val === 'section') {
        secGroup.style.display = 'flex';
        stuGroup.style.display = 'none';
      } else if (val === 'student') {
        secGroup.style.display = 'none';
        stuGroup.style.display = 'flex';
      } else {
        secGroup.style.display = 'none';
        stuGroup.style.display = 'none';
      }
    }
  </script>

{{-- TAB 6: NOTIFICATIONS INBOX --}}
@elseif($tab === 'notifications')
  <div class="cs-grid-2" style="align-items: start;">
    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title"><span>🔔</span> System & Campus Notices</h2>
          <p class="cs-panel-subtitle">Official announcements from administrators</p>
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
            <div class="cs-notice-sender">🏛️ Campus Administration</div>
          </div>
        @empty
          <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">No system notices at this time.</p>
        @endforelse
      </div>
    </div>

    <div class="cs-panel">
      <div class="cs-panel-header">
        <div>
          <h2 class="cs-panel-title"><span>📤</span> Sent Student Announcements</h2>
          <p class="cs-panel-subtitle">History of notices sent by you</p>
        </div>
        <div>
          <a href="{{ route('teacher.dashboard', ['tab' => 'send-notification']) }}" class="cs-button cs-button-sm">Compose</a>
        </div>
      </div>

      <div class="cs-notice-list">
        @forelse($sentNotifications as $notice)
          <div class="cs-notice-card">
            <div class="cs-notice-header">
              <span class="cs-notice-title">{{ $notice->title }}</span>
              <span class="cs-notice-time">{{ $notice->created_at->diffForHumans() }}</span>
            </div>
            <div class="cs-notice-body">{{ $notice->message }}</div>
            <div class="cs-notice-sender">
              🎯 Target: 
              @if($notice->recipient_type === 'section')
                Section {{ $notice->target_section }}
              @elseif($notice->recipient_type === 'student')
                Student: {{ $notice->targetUser->name ?? 'User #'.$notice->target_user_id }}
              @else
                All Students
              @endif
            </div>
          </div>
        @empty
          <p style="color: var(--text-muted); text-align: center; padding: 2rem 0;">You haven't sent any student announcements yet.</p>
        @endforelse
      </div>
    </div>
  </div>

{{-- TAB 7: ACCOUNT / CREDENTIALS --}}
@elseif($tab === 'profile')
  <div class="cs-panel" style="max-width: 600px; margin: 0 auto;">
    <div class="cs-panel-header">
      <div>
        <h2 class="cs-panel-title"><span>👤</span> Account & Credentials</h2>
        <p class="cs-panel-subtitle">Update your personal information, email address, and security password</p>
      </div>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="cs-form-grid">
      @csrf
      @method('PUT')

      <div class="cs-form-group">
        <label class="cs-form-label" for="name">Full Name</label>
        <input class="cs-input" id="name" name="name" value="{{ old('name', $teacher->name) }}" required>
      </div>

      <div class="cs-grid-2">
        <div class="cs-form-group">
          <label class="cs-form-label" for="user_id">Teacher / Employee ID</label>
          <input class="cs-input" id="user_id" name="user_id" value="{{ old('user_id', $teacher->user_id) }}" required>
        </div>
        <div class="cs-form-group">
          <label class="cs-form-label" for="email">Email Address</label>
          <input class="cs-input" type="email" id="email" name="email" value="{{ old('email', $teacher->email) }}" placeholder="teacher@campus.edu">
        </div>
      </div>

      <div class="cs-form-group">
        <label class="cs-form-label" for="department">Department / Faculty</label>
        <input class="cs-input" id="department" name="department" value="{{ old('department', $teacher->department) }}" placeholder="e.g. Computer Science Department">
      </div>

      <div style="border-top: 1px solid var(--border); padding-top: 1.25rem; margin-top: 0.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.25rem;">Change Password</h3>
        <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1rem;">Leave blank if you do not wish to change your password.</p>

        <div class="cs-grid-2">
          <div class="cs-form-group">
            <label class="cs-form-label" for="prof_password">New Password</label>
            <div class="cs-password-wrapper">
              <input class="cs-input" type="password" id="prof_password" name="password" minlength="6" placeholder="New password">
              <button type="button" class="cs-password-toggle" title="Toggle password visibility">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>

          <div class="cs-form-group">
            <label class="cs-form-label" for="prof_password_confirmation">Confirm Password</label>
            <div class="cs-password-wrapper">
              <input class="cs-input" type="password" id="prof_password_confirmation" name="password_confirmation" minlength="6" placeholder="Confirm password">
              <button type="button" class="cs-password-toggle" title="Toggle password visibility">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <button type="submit" class="cs-button cs-button-success" style="width: 100%; margin-top: 0.5rem;">
        <span>💾 Save Profile & Credentials</span>
      </button>
    </form>
  </div>
@endif

@endsection