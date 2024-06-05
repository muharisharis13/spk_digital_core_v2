<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentInstansi extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_instansi_id";
    protected $with = ["indent_instansi_log", "indent_instansi_payments", "indent_instansi_payment_refunds"];


    public function indent_instansi_log()
    {
        return $this->hasMany(IndentInstansiLog::class, "indent_instansi_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function motor()
    {
        return $this->belongsTo(Motor::class, "motor_id");
    }

    public function indent_instansi_payments()
    {
        return $this->hasMany(IndentInstansiPayment::class, "indent_instansi_id");
    }

    public function indent_instansi_payment_refunds()
    {
        return $this->hasMany(IndentInstansiPaymentRefund::class, "indent_instansi_id");
    }
}
