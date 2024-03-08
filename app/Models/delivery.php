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

    public function repair()
    {
        return $this->belongsTo(Repair::class, "repair_id");
    }
}
