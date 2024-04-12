<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkDeliveryDomicile extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_delivery_domicile_id";

    protected $with = ["file_sk"];

    public function file_sk()
    {
        return $this->hasMany(SpkDeliveryFileSk::class, "spk_delivery_domicile_id");
    }
}
