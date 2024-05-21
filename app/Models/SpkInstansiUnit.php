<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiUnit extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_unit_id";

    protected $with = ['unit', 'motor'];

    public function spk_instansi()
    {
        return $this->belongsTo(SpkInstansi::class, "spk_instansi_id");
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }
    public function motor()
    {
        return $this->belongsTo(Motor::class, "motor_id");
    }

    public function spk_instansi_unit_delivery()
    {
        return $this->hasOne(SpkInstansiUnitDelivery::class, "spk_instansi_unit_id");
    }
}
