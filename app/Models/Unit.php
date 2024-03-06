<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'unit';

    protected $fillable = [
        "unit_id",
        "unit_color",
        "unit_frame",
        "unit_engine",
        "unit_received_date",
        "unit_note",
        "unit_status",
        "shipping_order_id",
        "event_id",
        "motor_id",
        "dealer_neq_id",
        "dealer_id",
        "unit_code"
    ];

    protected $primaryKey = "unit_id";

    protected $hidden = [
        "unit_code"
    ];

    public function unit_log()
    {
        return $this->hasMany(UnitLog::class, "unit_id", "unit_id");
    }

    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, "dealer_neq_id");
    }
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function event()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    public function shipping_order()
    {
        return $this->belongsTo(ShippingOrder::class, "shipping_order_id");
    }

    public function motor()
    {
        return $this->belongsTo(Motor::class, "motor_id");
    }
}
