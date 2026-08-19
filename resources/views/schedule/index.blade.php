@extends('layouts.app')

@section('content')
<div class="cs-page-head">
  <h2>Visual Schedule Grid</h2>
</div>

<form method="GET" action="{{ route('schedule.index') }}" class="cs-controls">
  <input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" onchange="this.form.submit()">
  
  <div class="cs-filter-group">
    @foreach(['all' => 'All', 'room' => 'Rooms', 'lab' => 'Laboratories', 'facility' => 'Facilities', 'equipment' => 'Equipment'] as $key => $label)
      <button type="submit" name="type" value="{{ $key }}" class="cs-filter-btn {{ ($type ?? 'all') === $key ? 'active' : '' }}">
        {{ $label }}
      </button>
    @endforeach
  </div>
</form>

<div class="cs-grid-wrap">
  <div class="cs-grid-scroll">
    <div class="cs-grid-inner">
      <div class="cs-grid-header-row">
        <div class="cs-grid-label-col">Resource</div>
        <div class="cs-grid-hours">
          @for($m = 420; $m <= 1260; $m += 60)
            <div class="cs-hour-tick">{{ date('g:i A', mktime(0, $m)) }}</div>
          @endfor
        </div>
      </div>

      @if(isset($resources) && count($resources) > 0)
        @foreach($resources as $resource)
          <div class="cs-resource-row">
            <div class="cs-resource-label">
              <div class="name">{{ $resource->name }}</div>
              <div class="meta">{{ $resource->location }} {{ $resource->capacity ? '· ' . $resource->capacity . ' seats' : '' }}</div>
              <span class="cs-type-chip cs-type-{{ $resource->type }}">{{ strtoupper($resource->type) }}</span>
            </div>
            
            <div class="cs-track" style="width: 1120px;">
              @if(isset($bookings))
                @foreach($bookings->where('resource_id', $resource->id) as $b)
                  @php
                    $startMin = (strtotime($b->start_time) - strtotime('07:00')) / 60;
                    $duration = (strtotime($b->end_time) - strtotime($b->start_time)) / 60;
                    $left = $startMin * (80 / 60);
                    $width = max($duration * (80 / 60), 30);
                  @endphp
                  <div class="cs-block {{ $b->status }}" style="left: {{ $left }}px; width: {{ $width }}px;" title="{{ $b->purpose }} — {{ $b->user->name ?? 'User' }}">
                    <div class="p">{{ $b->purpose }}</div>
                    <div class="t">{{ date('g:i A', strtotime($b->start_time)) }} – {{ date('g:i A', strtotime($b->end_time)) }}</div>
                  </div>
                @endforeach
              @endif
            </div>
          </div>
        @endforeach
      @else
        <div style="padding: 2rem; text-align: center; color: #64748b;">
          No resources found.
        </div>
      @endif
    </div>
  </div>
</div>
@endsection