<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRepairReturn extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_repair_return_id";


    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function repair_return()
    {
        return $this->belongsTo(RepairReturn::class, "repair_return_id");
    }
}
