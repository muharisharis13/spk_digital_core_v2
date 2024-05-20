<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySpkInstansi extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_spk_instansi_id";

    public function spk_instansi()
    {
        return $this->belongsTo(SpkInstansi::class, "spk_instansi_id");
    }
    public function spk_instansi_unit_delivery()
    {
        return $this->belongsTo(SpkInstansiUnitDelivery::class, "spk_instansi_unit_delivery_id");
    }
}
