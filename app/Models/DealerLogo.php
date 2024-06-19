<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerLogo extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $primaryKey = "dealer_logo_id";

    // public function getLogoAttribute($value): string
    // {
    //     if ($value) {
    //         return asset('/storage/' . $value);
    //     } else {
    //         return "";
    //     }
    // }

    protected $appends = ['full_url'];

    public function getFullUrlAttribute()
    {
        return url('storage/' . $this->attributes['logo']);
    }
}
