<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentInstansiPayment extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_instansi_payment_id";
    protected $with = ["indent_instansi_payment_images", "bank"];


    public function indent_instansi_payment_images()
    {
        return $this->hasMany(IndentInstansiPaymentImage::class, "idnt_instansi_payment_id", "indent_instansi_payment_id");
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, "bank_id");
    }

    public function indent_instansi()
    {
        return $this->belongsTo(IndentInstansi::class, "indent_instansi_id");
    }
}
