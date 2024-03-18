<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairReturn extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_return_id";

    protected $with = ["repair_return_unit.repair_unit.unit", "dealer", "delivery_repair_return.delivery"];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function repair_return_unit()
    {
        return $this->hasMany(RepairReturnUnit::class, "repair_return_id");
    }

    public function delivery_repair_return()
    {
        return $this->hasOne(DeliveryRepairReturn::class, "repair_return_id");
    }
}
