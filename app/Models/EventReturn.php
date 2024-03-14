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

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }
}
