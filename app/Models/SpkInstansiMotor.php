<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiMotor extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $with = ['motor', 'color', 'motor.motor_pricelist'];
    protected $primaryKey = "spk_instansi_motor_id";

    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id', 'color_id');
    }

    public function motor()
    {
        return $this->belongsTo(Motor::class, 'motor_id', 'motor_id');
    }
}
