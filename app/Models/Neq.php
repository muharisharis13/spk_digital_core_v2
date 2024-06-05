<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neq extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "neq_id";

    public function neq_unit()
    {
        return $this->hasMany(NeqUnit::class, "neq_id");
    }

    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, "dealer_neq_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function neq_log()
    {
        return $this->hasMany(NeqLog::class, "neq_id");
    }

    public function deliver_neq()
    {
        return $this->hasOne(DeliveryNeq::class, "neq_id");
    }
}
