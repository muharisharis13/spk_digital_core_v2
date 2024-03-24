<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerNeq extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'dealer_neq';
    protected $fillable = [
        "dealer_neq_id",
        "dealer_neq_name",
        "dealer_neq_address",
        "dealer_neq_phone_number",
        "dealer_neq_code",
        "dealer_neq_city",
        "dealer_id"
    ];

    protected $primaryKey = "dealer_neq_id";

    public function neq()
    {
        return $this->hasMany(Neq::class, "dealer_neq_id");
    }
}
