<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiUnitDelivery extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_unit_delivery_id";
}
