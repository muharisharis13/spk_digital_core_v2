<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'dealer';

    protected $fillable = [
        "dealer_id",
        "dealer_name",
        "dealer_address",
        "dealer_phone_number",
        "dealer_code",
        "dealer_city",
        "dealer_type",
        "dealer_location_alias"
    ];

    protected $primaryKey = "dealer_id";

    public function dealer_logo()
    {
        return $this->hasOne(DealerLogo::class, "dealer_id");
    }
}
