<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'motor';
    protected $fillable = [
        "motor_id",
        "motor_name",
        "motor_status",
        "motor_code"
    ];

    protected $primaryKey = "motor_id";
    protected $with = ["motor_pricelist"];

    public function motor_pricelist()
    {
        return $this->hasMany(PricelistMotor::class, "motor_id");
    }
}
