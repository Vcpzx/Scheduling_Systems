<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_code',
        'subject_name',
        'section',
        'semester',
        'academic_year',
        'resource_id',
        'room_name',
        'days',
        'start_time',
        'end_time',
        'notes',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'class_schedule_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_enrollments', 'class_schedule_id', 'student_id')
            ->withTimestamps();
    }

    public function matchesDay(string $dayName): bool
    {
        $days = strtolower($this->days);
        $dayName = strtolower($dayName);

        if (str_contains($days, $dayName)) {
            return true;
        }
        if (str_contains($days, 'mwf') && in_array($dayName, ['monday', 'wednesday', 'friday'])) {
            return true;
        }
        if (str_contains($days, 'tth') && in_array($dayName, ['tuesday', 'thursday'])) {
            return true;
        }
        if (str_contains($days, 'sat') && $dayName === 'saturday') {
            return true;
        }
        if (str_contains($days, 'sun') && $dayName === 'sunday') {
            return true;
        }

        return false;
    }

    public function isCurrentClass(): bool
    {
        $now = Carbon::now();
        $todayName = $now->format('l');

        if (!$this->matchesDay($todayName)) {
            return false;
        }

        $currentTime = $now->format('H:i:s');
        $startTime = Carbon::parse($this->start_time)->format('H:i:s');
        $endTime = Carbon::parse($this->end_time)->format('H:i:s');

        return ($currentTime >= $startTime && $currentTime <= $endTime);
    }

    public function isUpcomingToday(): bool
    {
        $now = Carbon::now();
        $todayName = $now->format('l');

        if (!$this->matchesDay($todayName)) {
            return false;
        }

        $currentTime = $now->format('H:i:s');
        $startTime = Carbon::parse($this->start_time)->format('H:i:s');

        return ($startTime > $currentTime);
    }

    public function isStartingSoon(): bool
    {
        $now = Carbon::now();
        $todayName = $now->format('l');

        if (!$this->matchesDay($todayName)) {
            return false;
        }

        $classStart = Carbon::parse($now->format('Y-m-d') . ' ' . $this->start_time);
        $diffInMinutes = $now->diffInMinutes($classStart, false);

        return ($diffInMinutes >= 0 && $diffInMinutes <= 60);
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('g:i A', strtotime($this->start_time)) . ' - ' . date('g:i A', strtotime($this->end_time));
    }

    public function getEffectiveRoomAttribute(): string
    {
        if ($this->resource) {
            return $this->resource->name . ($this->resource->location ? ' (' . $this->resource->location . ')' : '');
        }
        return $this->room_name ?: 'TBA';
    }
}