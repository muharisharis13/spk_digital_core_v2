<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairUnitList extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_unit_list_id";



    public function repair()
    {
        return $this->belongsTo(Repair::class, "repair_id", "repair_id");
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id", "unit_id");
    }
}
