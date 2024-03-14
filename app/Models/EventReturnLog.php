<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventReturnLog extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "event_return_id";


    public function event_return()
    {
        return $this->belongsTo(EventReturn::class, "event_return_id");
    }
}
