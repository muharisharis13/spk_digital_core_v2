<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiDelivery extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_delivery_id";



    public function spk_instansi_delivery_file()
    {
        return $this->hasMany(SpkInstansiDeliveryFile::class, "spk_instansi_delivery_id");
    }
}
