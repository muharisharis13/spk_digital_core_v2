<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeqReturn extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "neq_return_id";

    public function neq_return_unit()
    {
        return $this->hasMany(NeqReturnUnit::class, "neq_return_id");
    }

    public function delivery_neq_return()
    {
        return $this->hasOne(DeliveryNeqReturn::class, "neq_return_id");
    }

    public function dealer_neq()
    {
        return $this->hasOne(DealerNeq::class, "neq_return_id");
    }

    public function neq_return_log()
    {
        return $this->hasMany(NeqReturnLog::class, "neq_return_id");
    }
}
