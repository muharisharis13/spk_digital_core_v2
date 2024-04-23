<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndentPaymentImage extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "indent_payment_image_id";


    protected function getIndentPaymentImgAttribute($value): string
    {
        if ($value) {
            return asset('/storage/' . $value);
        } else {
            return "";
        }
    }
}
