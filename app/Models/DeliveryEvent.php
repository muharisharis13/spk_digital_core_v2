<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryEvent extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_event_id";

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }
}
