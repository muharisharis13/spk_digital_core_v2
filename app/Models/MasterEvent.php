<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterEvent extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "master_event_id";
}
