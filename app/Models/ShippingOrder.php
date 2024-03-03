<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'shipping_order';
    protected $fillable = [
        "shipping_order_id",
        "shipping_order_number",
        "shipping_order_delivery_number",
        "shipping_order_status",
        "shipping_order_shipping_date",
        "dealer_id"
    ];

    protected $primaryKey = "shipping_order_id";

    public function unit(){
        return $this->hasMany(Unit::class,"shipping_order_id","shipping_order_id");
    }

    public function dealer(){
        return $this->belongsTo(Dealer::class,"dealer_id");
    }
}
