<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indent extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_id";

    public function motor()
    {
        return $this->belongsTo(Motor::class, "motor_id");
    }
    public function color()
    {
        return $this->belongsTo(Color::class, "color_id");
    }
    public function micro_finance()
    {
        return $this->belongsTo(MicroFinance::class, "micro_finance_id");
    }
    public function leasing()
    {
        return $this->belongsTo(Leasing::class, "leasing_id");
    }


    public function indent_payment()
    {
        return $this->hasMany(IndentPayment::class, "indent_id");
    }
    public function indent_log()
    {
        return $this->hasMany(IndentLog::class, "indent_id");
    }

    public function indent_payment_refund()
    {
        return $this->hasMany(IndentPaymentRefund::class, "indent_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }

    public function spk_general()
    {
        return $this->hasOne(SpkGeneral::class, "indent_id");
    }
}
