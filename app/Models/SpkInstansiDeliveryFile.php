<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiDeliveryFile extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_delivery_file_id";

    protected function getFilesAttribute($value): string
    {
        if ($value) {
            return asset('/storage/' . $value);
        } else {
            return "";
        }
    }
}
