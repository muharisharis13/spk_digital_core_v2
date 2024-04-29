<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturUnitList extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $with = ["unit.motor"];

    protected $primaryKey = "retur_unit_list_id";

    public function retur_unit()
    {
        return $this->belongsTo(ReturUnit::class, "retur_unit_id");
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }
}
