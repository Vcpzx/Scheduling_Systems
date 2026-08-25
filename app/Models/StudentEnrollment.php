<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_schedule_id',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }
}