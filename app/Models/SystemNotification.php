<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $fillable = ['created_by', 'title', 'message'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
