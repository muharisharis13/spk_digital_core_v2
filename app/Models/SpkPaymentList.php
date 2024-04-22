<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkPaymentList extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_payment_list_id";
    protected $with = ["spk_payment_list_img"];

    public function spk_payment_list_img()
    {
        return $this->hasMany(SpkPaymentListImage::class, "spk_payment_list_id");
    }
}
