<?php

namespace App\Support;

use App\Models\ClassSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WeeklyScheduleCalendar
{
    public static function build(Collection $schedules, Carbon $weekStart): array
    {
        $days = [];

        foreach (range(0, 6) as $offset) {
            $date = $weekStart->copy()->addDays($offset);
            $events = $schedules
                ->filter(fn (ClassSchedule $schedule) => $schedule->matchesDay($date->format('l')))
                ->map(function (ClassSchedule $schedule) {
                    $start = self::minutes($schedule->start_time);
                    $end = self::minutes($schedule->end_time);

                    if ($start >= 1020 || $end <= 420) {
                        return null;
                    }

                    $visibleStart = max($start, 420);
                    $visibleEnd = min($end, 1020);

                    return [
                        'schedule' => $schedule,
                        'start' => $visibleStart,
                        'end' => $visibleEnd,
                        'top' => (($visibleStart - 420) / 30) * 40,
                        'height' => max((($visibleEnd - $visibleStart) / 30) * 40, 24),
                    ];
                })
                ->filter()
                ->sortBy('start')
                ->values()
                ->all();

            $days[] = [
                'date' => $date,
                'events' => self::assignLanes($events),
            ];
        }

        return $days;
    }

    private static function assignLanes(array $events): array
    {
        $groups = [];
        $group = [];
        $groupEnd = null;

        foreach ($events as $event) {
            if ($group && $event['start'] >= $groupEnd) {
                $groups[] = $group;
                $group = [];
                $groupEnd = null;
            }

            $group[] = $event;
            $groupEnd = max($groupEnd ?? $event['end'], $event['end']);
        }

        if ($group) {
            $groups[] = $group;
        }

        $positioned = [];

        foreach ($groups as $group) {
            $laneEnds = [];

            foreach ($group as $event) {
                $lane = 0;
                while (isset($laneEnds[$lane]) && $laneEnds[$lane] > $event['start']) {
                    $lane++;
                }

                $laneEnds[$lane] = $event['end'];
                $event['lane'] = $lane;
                $event['lane_count'] = 0;
                $positioned[] = $event;
            }

            $laneCount = count($laneEnds);
            $groupEventCount = count($group);

            for ($index = count($positioned) - $groupEventCount; $index < count($positioned); $index++) {
                $positioned[$index]['lane_count'] = $laneCount;
            }
        }

        return $positioned;
    }

    private static function minutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return ($hours * 60) + $minutes;
    }
}
