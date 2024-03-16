<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairReturnUnit extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_return_unit_id";
    protected $with = ["repair_unit.unit.motor"];

    public function repair_unit()
    {
        return $this->belongsTo(RepairUnitList::class, "repair_unit_list_id");
    }
}
