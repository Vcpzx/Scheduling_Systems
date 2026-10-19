<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const COURSES = ['BSIS', 'BSCS', 'BSIT'];

    public const SECTIONS = [
        '1A', '2A', '3A', '4A',
        '1B', '2B', '3B', '4B',
        '1C', '2C', '3C', '4C',
        '1D', '2D', '3D', '4D',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'section',
        'course',
        'department',
        'role',
        'status',
        'password',
        'decided_by',
        'decided_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'decided_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function getCourseSectionAttribute(): ?string
    {
        $courseSection = trim(implode(' ', array_filter([$this->course, $this->section])));

        return $courseSection !== '' ? $courseSection : null;
    }

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'teacher_id');
    }

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id');
    }

    public function enrolledClasses()
    {
        return $this->belongsToMany(ClassSchedule::class, 'student_enrollments', 'student_id', 'class_schedule_id')
            ->withTimestamps();
    }

    public function sentNotifications()
    {
        return $this->hasMany(UserNotification::class, 'sender_id');
    }

    public function receivedNotifications()
    {
        return UserNotification::where(function ($query) {
            $query->where('recipient_type', 'all');
            if ($this->isStudent()) {
                if ($this->section) {
                    $section = $this->course_section;
                    $query->orWhere(function ($q) use ($section) {
                        $q->where('recipient_type', 'section')
                          ->where('target_section', $section);
                    });
                }
                $query->orWhere(function ($q) {
                    $q->where('recipient_type', 'student')
                      ->where('target_user_id', $this->id);
                });
            }
        })->latest();
    }
}