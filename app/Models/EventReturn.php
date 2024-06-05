<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReturn extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "event_return_id";

    protected $hidden = ["event_id"];

    public function master_event()
    {
        return $this->belongsTo(MasterEvent::class, "master_event_id");
    }

    public function event_return_unit()
    {
        return $this->hasMany(EventReturnListUnit::class, "event_return_id");
    }

    public function event_return_log()
    {
        return $this->hasMany(EventReturnLog::class, "event_return_id");
    }

    public function delivery_event_return()
    {
        return $this->hasOne(DeliveryEventReturn::class, "event_return_id");
    }
}
