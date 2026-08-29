@extends('layouts.app')

@section('title', 'Master Campus Schedule | Campus Scheduler')

@section('content')
<div class="cs-header-banner">
  <div>
    <h1>Master Campus Schedule</h1>
    <p>Browse live room bookings, classroom assignments, and resource occupancy across campus facilities.</p>
  </div>
  <div>
    @if(Auth::user()->isTeacher())
      <a href="{{ route('teacher.dashboard') }}" class="cs-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
        <span>Teacher Dashboard</span>
      </a>
    @elseif(Auth::user()->isStudent())
      <a href="{{ route('student.dashboard') }}" class="cs-button">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <span>Student Dashboard</span>
      </a>
    @endif
  </div>
</div>

<div class="cs-panel">
  <div class="cs-panel-header">
    <div>
      <h2 class="cs-panel-title">Visual Resource Occupancy</h2>
      <p class="cs-panel-subtitle">Displaying scheduled reservations for {{ date('l, F j, Y', strtotime($date)) }}</p>
    </div>
  </div>

  <form method="GET" action="{{ route('schedule.index') }}" class="cs-controls" style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <input type="date" name="date" class="cs-input" style="width: auto;" value="{{ $date }}" onchange="this.form.submit()">
    <div class="cs-filter-group">
      @foreach(['all' => 'All Resources', 'room' => 'Classrooms', 'lab' => 'Laboratories', 'facility' => 'Facilities', 'equipment' => 'Equipment'] as $key => $label)
        <button type="submit" name="type" value="{{ $key }}" class="cs-filter-btn {{ $type === $key ? 'active' : '' }}">{{ $label }}</button>
      @endforeach
    </div>
  </form>

  <div class="cs-grid-wrap">
    <div class="cs-grid-inner">
      <div class="cs-grid-header-row">
        <div class="cs-grid-label-col">Resource / Space</div>
        <div class="cs-grid-hours">
          @for($m = 420; $m <= 1260; $m += 60)
            <div class="cs-hour-tick">{{ date('g:i A', mktime(0, $m)) }}</div>
          @endfor
        </div>
      </div>

      @forelse($resources as $resource)
        <div class="cs-resource-row">
          <div class="cs-resource-label">
            <div style="font-weight: 700; color: var(--text-main);">{{ $resource->name }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $resource->location }} {{ $resource->capacity ? '· '.$resource->capacity.' seats' : '' }}</div>
            <span class="cs-badge cs-badge-neutral" style="margin-top: 0.35rem;">{{ strtoupper($resource->type) }}</span>
          </div>
          <div class="cs-track" style="width: 1120px;">
            @foreach($bookings->where('resource_id', $resource->id) as $booking)
              @php 
                $startMin = (strtotime($booking->start_time) - strtotime('07:00')) / 60; 
                $duration = (strtotime($booking->end_time) - strtotime($booking->start_time)) / 60; 
              @endphp
              <div class="cs-booking-block {{ $booking->status }}" style="left: {{ $startMin * (80 / 60) }}px; width: {{ max($duration * (80 / 60), 30) }}px;" title="{{ $booking->purpose }}">
                <div style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $booking->purpose }}</div>
                <div style="font-size: 0.7rem; opacity: 0.9;">{{ $booking->user?->name ?? 'Instructor' }} · {{ date('g:i A', strtotime($booking->start_time)) }}</div>
              </div>
            @endforeach
          </div>
        </div>
      @empty
        <div style="padding: 2.5rem; text-align: center; color: var(--text-muted);">No campus resources found matching criteria.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
