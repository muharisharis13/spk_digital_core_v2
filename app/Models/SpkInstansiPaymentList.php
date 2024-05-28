<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkInstansiPaymentList extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_instansi_payment_list_id";

    public function bank()
    {
        return $this->belongsTo(Bank::class, "bank_id");
    }

    public function spk_instansi_payment_list_file()
    {
        return $this->hasMany(SpkInstansiPaymentListFile::class, "payment_list_id", "spk_instansi_payment_list_id");
    }
}
