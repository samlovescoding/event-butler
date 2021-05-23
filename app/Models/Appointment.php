<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function getDateAttribute()
    {
        return explode(" ", $this->slot_time)[0];
    }
    public function getTimeAttribute()
    {
        return explode(" ", $this->slot_time)[1];
    }
}
