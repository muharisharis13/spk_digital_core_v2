<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistMotor extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "pricelist_motor_id";
    protected $with = ["dealer", "dealer_neq"];

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, "dealer_neq_id");
    }
}
