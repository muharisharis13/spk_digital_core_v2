<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNeqReturn extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_neq_return_id";

    protected $with = ["delivery", "delivery_neq_return"];


    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function neq_return()
    {
        return $this->belongsTo(NeqReturn::class, "neq_return");
    }
}
