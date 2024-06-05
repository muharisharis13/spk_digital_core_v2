<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNeq extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_neq_id";

    // protected $with = ["delivery", "delivery_neq"];

    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function neq()
    {
        return $this->belongsTo(Neq::class, "neq_id");
    }
}
