<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        "shipping_order_id",
        "shipping_order_number",
        "shipping_order_delivery_number",
        "shipping_order_status",
        "shipping_order_shipping_date",
        "dealer_id"
    ];

    protected $primaryKey = "shipping_order_id";
}
