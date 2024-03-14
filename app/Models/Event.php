<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'event';

    protected $guarded = [];

    protected $primaryKey = "event_id";
    protected $with = ["event_log"];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function event_unit()
    {
        return $this->hasMany(EventListUnit::class, "event_id", "event_id");
    }

    public function event_log()
    {
        return $this->hasMany(EventLog::class, "event_id", "event_id");
    }
}
