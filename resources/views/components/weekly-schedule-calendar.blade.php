@php
  $calendarRouteParameters = $calendarRouteParameters ?? [];
  $previousWeek = $calendarWeekStart->copy()->subWeek()->toDateString();
  $nextWeek = $calendarWeekStart->copy()->addWeek()->toDateString();
@endphp

<section class="cs-panel">
  <div class="cs-panel-header">
    <div>
      <h2 class="cs-panel-title">Weekly Class Schedule</h2>
      <p class="cs-panel-subtitle">
        {{ $calendarTerm ? $calendarTerm->semester.' · '.$calendarTerm->academic_year : 'No academic term configured' }}
        · {{ $calendarWeekStart->format('M j') }}–{{ $calendarWeekStart->copy()->addDays(6)->format('M j, Y') }}
      </p>
    </div>
    <div class="cs-week-controls">
      <a class="cs-button cs-button-secondary cs-button-sm" href="{{ route($calendarRouteName, $calendarRouteParameters + ['week' => $previousWeek, 'room_id' => $calendarRoomId]) }}" aria-label="Previous week">&larr;</a>
      <form method="GET" action="{{ route($calendarRouteName, $calendarRouteParameters) }}">
        @foreach($calendarRouteParameters as $key => $value)
          <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <label class="cs-visually-hidden" for="calendar-week">Choose week</label>
        <input class="cs-input" id="calendar-week" type="date" name="week" value="{{ $calendarWeekStart->toDateString() }}" onchange="this.form.submit()">
        @if($calendarResources->isNotEmpty())
          <label class="cs-visually-hidden" for="calendar-room">Filter by room</label>
          <select class="cs-select" id="calendar-room" name="room_id" onchange="this.form.submit()">
            <option value="">All rooms</option>
            @foreach($calendarResources as $resource)
              <option value="{{ $resource->id }}" {{ (string) $calendarRoomId === (string) $resource->id ? 'selected' : '' }}>{{ $resource->name }}</option>
            @endforeach
          </select>
        @endif
      </form>
      <a class="cs-button cs-button-secondary cs-button-sm" href="{{ route($calendarRouteName, $calendarRouteParameters + ['week' => $nextWeek, 'room_id' => $calendarRoomId]) }}" aria-label="Next week">&rarr;</a>
    </div>
  </div>

  @if(Auth::user()->isAdmin() && $calendarTerm && !$calendarTerm->exists)
    <p class="cs-term-setup-note">Add term start and end dates under Academic Terms to enable automatic semester changes.</p>
  @endif
  @if(Auth::user()->isAdmin() && $calendarRoomId)
    <p class="cs-panel-subtitle cs-room-availability-note">Filter by a room to check its available times. Empty periods have no scheduled classes.</p>
  @endif

  <div class="cs-weekly-calendar-scroll">
    <div class="cs-weekly-calendar">
      <div class="cs-calendar-corner">Time</div>
      @foreach($calendarDays as $day)
        <div class="cs-calendar-day-heading">
          <span>{{ $day['date']->format('l') }}</span>
          <strong>{{ $day['date']->format('M j') }}</strong>
        </div>
      @endforeach

      <div class="cs-calendar-time-axis">
        @for($minutes = 420; $minutes < 1020; $minutes += 30)
          <div class="cs-calendar-time-tick">{{ date('g:i A', mktime(0, $minutes)) }}</div>
        @endfor
        <div class="cs-calendar-time-end">5:00 PM</div>
      </div>
      @foreach($calendarDays as $day)
        <div class="cs-calendar-day-track">
          @foreach($day['events'] as $event)
            @php
              $schedule = $event['schedule'];
              $laneCount = $event['lane_count'];
              $eventTitle = $schedule->subject_code.' - '.$schedule->subject_name.' · '.$schedule->effective_room.' · '.($schedule->teacher->name ?? 'Professor not assigned');
            @endphp
            <article class="cs-calendar-event" style="top: {{ $event['top'] }}px; height: {{ $event['height'] }}px; left: calc((100% / {{ $laneCount }}) * {{ $event['lane'] }} + 3px); width: calc(100% / {{ $laneCount }} - 6px);" title="{{ $eventTitle }}">
              <strong>{{ $schedule->subject_code }} · {{ $schedule->subject_name }}</strong>
              <span>{{ $schedule->effective_room }}</span>
              <span>{{ $schedule->teacher->name ?? 'Professor not assigned' }}</span>
            </article>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  @if(collect($calendarDays)->every(fn ($day) => empty($day['events'])))
    <p class="cs-calendar-empty">No classes are scheduled in this week for the selected term and room.</p>
  @endif
</section>
