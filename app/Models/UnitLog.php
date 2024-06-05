<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitLog extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "unit_log_id";


    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
