<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventListUnit extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "event_list_unit_id";

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }
}
