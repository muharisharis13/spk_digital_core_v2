<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiUnitDelivery extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $with = ['dealer_neq'];

    protected $primaryKey = "spk_instansi_unit_delivery_id";

    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, 'dealer_neq_id', 'dealer_neq_id');
    }
}
