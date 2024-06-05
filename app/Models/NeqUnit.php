<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeqUnit extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "neq_unit_id";

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }

    public function neq()
    {
        return $this->belongsTo(Neq::class, "neq_id");
    }
}
