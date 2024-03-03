<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Motor extends Model
{
    use HasFactory;
    

    protected $fillable = [
        "motor_id",
        "motor_name",
        "motor_status",
        "motor_code"
    ];

    protected $primaryKey="motor_id";
}
