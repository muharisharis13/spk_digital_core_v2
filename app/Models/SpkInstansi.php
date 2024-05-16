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
    protected $with = ["spk_instansi_delivery.spk_instansi_delivery_file"];

    public function spk_instansi_delivery()
    {
        return $this->hasOne(SpkInstansiDelivery::class, "spk_instansi_id");
    }
}
