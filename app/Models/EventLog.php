<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "event_log_id";

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
