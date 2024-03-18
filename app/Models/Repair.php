<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "repair_id";


    public function main_dealer()
    {
        return $this->belongsTo(MainDealer::class, "main_dealer_id");
    }
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function repair_unit()
    {
        return $this->hasMany(RepairUnitList::class, "repair_id", "repair_id");
    }
    public function repair_log()
    {
        return $this->hasMany(RepairLog::class, "repair_id", "repair_id");
    }
    public function delivery_repair()
    {
        return $this->hasOne(DeliveryRepair::class, "repair_id", "repair_id");
    }
}
