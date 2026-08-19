<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $fillable = [
        'resource_id',
        'user_id',
        'date',
        'start_time',
        'end_time',
        'purpose',
        'status',
        'decided_by',
        'decided_at',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}