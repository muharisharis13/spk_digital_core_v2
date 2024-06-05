<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiUnitDelivery extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $with = ['dealer_neq', 'spk_instansi_unit_deliv_file', 'spk_instansi_unit'];

    protected $primaryKey = "spk_instansi_unit_delivery_id";

    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, 'dealer_neq_id', 'dealer_neq_id');
    }
    public function spk_instansi_unit()
    {
        return $this->belongsTo(SpkInstansiUnit::class, 'spk_instansi_unit_id', 'spk_instansi_unit_id');
    }

    public function spk_instansi_unit_deliv_file()
    {
        return $this->hasMany(SpkInstansiUnitDeliveryFile::class, "spk_instansi_unit_deliv_id", "spk_instansi_unit_delivery_id",);
    }

    public function delivery_spk_instansi_partial()
    {
        return $this->hasOne(DeliverySpkInstansi::class, "spk_instansi_unit_delivery_id", "spk_instansi_unit_delivery_id",);
    }
}
