<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRepair extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_repair_id";


    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function repair()
    {
        return $this->belongsTo(Repair::class, "repair_id");
    }
}
