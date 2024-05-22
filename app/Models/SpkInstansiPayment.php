<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiPayment extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_payment_id";

    protected $with = ["spk_instansi_payment_list", "spk_instansi_payment_log"];

    public function spk_instansi_payment_log()
    {
        return $this->hasMany(SpkInstansiPaymentLog::class, "spk_instansi_payment_id");
    }

    public function spk_instansi_payment_list()
    {
        return $this->hasMany(SpkInstansiPaymentList::class, "spk_instansi_payment_id");
    }
}
