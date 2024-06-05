<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkExcessFunds extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_excess_fund_id";
    // protected $with = ["spk"];


    public function spk()
    {
        return $this->belongsTo(Spk::class, "spk_id");
    }
}
