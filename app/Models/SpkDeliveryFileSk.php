<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkDeliveryFileSk extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_delivery_file_sk_id";

    public function getFileAttribute($value): string
    {
        if ($value) {
            return asset('/storage/' . $value);
        } else {
            return "";
        }
    }
}
