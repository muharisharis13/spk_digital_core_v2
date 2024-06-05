<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentPayment extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_payment_id";

    protected $with = ["indent_payment_img"];


    public function bank()
    {
        return $this->belongsTo(Bank::class, "bank_id");
    }

    public function indent()
    {
        return $this->belongsTo(Indent::class, "indent_id");
    }


    public function indent_payment_img()
    {
        return $this->hasMany(IndentPaymentImage::class, "indent_payment_id");
    }


    protected function getIndentPaymentImgAttribute($value): string
    {
        if ($value) {
            return asset('/storage/' . $value);
        } else {
            return "";
        }
    }
}
