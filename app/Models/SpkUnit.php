<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkUnit extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_unit_id";

    public function unit()
    {
        return $this->belongsTo(Unit::class, "unit_id");
    }
    public function motor()
    {
        return $this->belongsTo(Motor::class, "motor_id");
    }

    public function color()
    {
        return $this->belongsTo(Color::class, "color_id");
    }
}
