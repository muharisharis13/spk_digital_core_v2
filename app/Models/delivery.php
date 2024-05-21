<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "delivery_id";

    protected $with = ["delivery_log.user"];

    public function delivery_log()
    {
        return $this->hasMany(deliveryLog::class, "delivery_id", "delivery_id");
    }
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function delivery_repair()
    {
        return $this->hasOne(DeliveryRepair::class, "delivery_id");
    }

    public function delivery_repair_return()
    {
        return $this->hasOne(DeliveryRepairReturn::class, "delivery_id");
    }

    public function delivery_event()
    {
        return $this->hasOne(DeliveryEvent::class, "delivery_id");
    }
    public function delivery_event_return()
    {
        return $this->hasOne(DeliveryEventReturn::class, "delivery_id");
    }

    public function delivery_neq()
    {
        return $this->hasOne(DeliveryNeq::class, "delivery_id");
    }
    public function delivery_neq_return()
    {
        return $this->hasOne(DeliveryNeqReturn::class, "delivery_id", "delivery_id");
    }
    public function delivery_spk()
    {
        return $this->hasOne(DeliverySpk::class, "delivery_id", "delivery_id");
    }
    public function delivery_spk_instansi_partial()
    {
        return $this->hasMany(DeliverySpkInstansi::class, "delivery_id", "delivery_id");
    }
    public function delivery_spk_instansi()
    {
        return $this->hasOne(DeliverySpkInstansi::class, "delivery_id", "delivery_id");
    }
}
