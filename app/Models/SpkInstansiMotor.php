<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiMotor extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_motor_id";
}
