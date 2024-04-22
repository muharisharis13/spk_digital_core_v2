<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkPayment extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_payment_id";

    protected $with = ["spk_payment_list", "spk_payment_log"];


    public function spk()
    {
        return $this->belongsTo(Spk::class, "spk_id");
    }

    public function spk_payment_log()
    {
        return $this->hasMany(SpkPaymentLog::class, "spk_payment_id");
    }

    public function spk_payment_list()
    {
        return $this->hasMany(SpkPaymentList::class, "spk_payment_id");
    }
}
