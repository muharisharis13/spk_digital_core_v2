<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricelistMotorHistories extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $with = ["user"];

    protected $primaryKey = "pricelist_motor_histories_id";


    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
