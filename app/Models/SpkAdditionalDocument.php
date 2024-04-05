<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkAdditionalDocument extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_additional_document_id";

    protected function getDocumentNameAttributeKK($value): string
    {
        if ($value) {
            return asset('/storage/' . $value);
        } else {
            return "";
        }
    }
    // protected function getDocumentNameAttributeKTP($value): string
    // {
    //     if ($value) {
    //         return asset('/storage/' . $value);
    //     } else {
    //         return "";
    //     }
    // }
}
