<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_id";
    protected $with = ["spk_legal", "spk_pricing", "spk_unit.unit", "spk_transaction", "spk_general.indent", "spk_general.sales", "spk_general.dealer", "spk_general.dealer_neq"];

    public function spk_transaction()
    {
        return $this->hasOne(SpkTransaction::class, "spk_id");
    }
    public function spk_general()
    {
        return $this->hasOne(SpkGeneral::class, "spk_id");
    }

    public function spk_unit()
    {
        return $this->hasOne(SpkUnit::class, "spk_id");
    }

    public function spk_pricing()
    {
        return $this->hasOne(SpkPricing::class, "spk_id");
    }

    public function spk_legal()
    {
        return $this->hasOne(SpkLegal::class, "spk_id");
    }
}
