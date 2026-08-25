<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['name', 'type', 'location', 'capacity'];

    public function bookingRequests()
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class);
    }
}