<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliverySpk extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_spk_id";

    public function delivery()
    {
        return $this->belongsTo(delivery::class, "delivery_id");
    }

    public function spk()
    {
        return $this->belongsTo(Spk::class, "spk_id");
    }
}
