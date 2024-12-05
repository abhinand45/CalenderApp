<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'start_time', 'end_time', 'user_id'];

    protected $dates = ['start', 'end'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
