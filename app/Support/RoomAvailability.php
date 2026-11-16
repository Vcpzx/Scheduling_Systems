<?php

namespace App\Support;

use App\Models\AcademicTerm;
use App\Models\BookingRequest;
use App\Models\ClassSchedule;
use App\Models\Resource;
use Carbon\Carbon;

class RoomAvailability
{
    public static function isAvailable(
        Resource $resource,
        string $date,
        string $startTime,
        string $endTime,
        ?int $ignoreBookingId = null
    ): bool
    {
        $bookingConflict = BookingRequest::where('resource_id', $resource->id)
            ->whereDate('date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->when($ignoreBookingId, fn ($query) => $query->where('id', '<>', $ignoreBookingId))
            ->exists();

        if ($bookingConflict) {
            return false;
        }

        $term = AcademicTerm::whereDate('starts_on', '<=', $date)
            ->whereDate('ends_on', '>=', $date)
            ->first();

        if (!$term && AcademicTerm::exists()) {
            return true;
        }

        $term ??= AcademicTerm::current();
        $dayName = Carbon::parse($date)->format('l');

        return !ClassSchedule::with('resource')
            ->when($term, fn ($query) => $query
                ->where('semester', $term->semester)
                ->where('academic_year', $term->academic_year))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->get()
            ->contains(function (ClassSchedule $schedule) use ($resource, $dayName) {
                if (!$schedule->matchesDay($dayName)) {
                    return false;
                }

                return (int) $schedule->resource_id === (int) $resource->id
                    || strcasecmp((string) $schedule->room_name, $resource->name) === 0;
            });
    }
}
