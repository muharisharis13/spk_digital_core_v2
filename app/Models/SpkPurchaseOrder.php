<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkPurchaseOrder extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_purchase_order_id";

    protected $with = ["spk_purchase_order_file"];


    public function spk_purchase_order_file()
    {
        return $this->hasMany(SpkPurchaseOrderFile::class, "spk_purchase_order_id");
    }
}
