<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReturnListUnit extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "event_return_list_unit_id";

    protected $with = ["event_list_unit.unit.motor"];

    public function event_return()
    {
        return $this->belongsTo(EventReturn::class, "event_return_id");
    }

    public function event_list_unit()
    {
        return $this->belongsTo(EventListUnit::class, "event_list_unit_id");
    }
}
