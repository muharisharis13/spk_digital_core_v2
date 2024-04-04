<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotorBrand extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "motor_brand_id";
}
