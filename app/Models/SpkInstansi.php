<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansi extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_id";
    protected $with = ["spk_instansi_additional", "spk_instansi_additional_file", "spk_instansi_legal", "spk_instansi_motor", "spk_instansi_general", "spk_instansi_delivery.spk_instansi_delivery_file", "spk_instansi_log"];

    public function spk_instansi_delivery()
    {
        return $this->hasOne(SpkInstansiDelivery::class, "spk_instansi_id");
    }

    public function spk_instansi_general()
    {
        return $this->hasOne(SpkInstansiGeneral::class, "spk_instansi_id");
    }

    public function spk_instansi_legal()
    {
        return $this->hasOne(SpkInstansiLegal::class, "spk_instansi_id");
    }

    public function spk_instansi_motor()
    {
        return $this->hasMany(SpkInstansiMotor::class, "spk_instansi_id");
    }
    public function spk_instansi_additional_file()
    {
        return $this->hasMany(SpkInstansiAdditionalFile::class, "spk_instansi_id");
    }

    public function spk_instansi_log()
    {
        return $this->hasMany(SpkInstansiLog::class, "spk_instansi_id");
    }

    public function spk_instansi_additional()
    {
        return $this->hasMany(SpkInstansiAdditional::class, "spk_instansi_id");
    }
}
