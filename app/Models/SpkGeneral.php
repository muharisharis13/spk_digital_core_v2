<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkGeneral extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];

    protected $primaryKey = "spk_general_id";

    public function indent()
    {
        return $this->belongsTo(Indent::class, "indent_id");
    }

    public function sales()
    {
        return $this->belongsTo(Sales::class, "sales_id");
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, "dealer_id");
    }
    public function dealer_neq()
    {
        return $this->belongsTo(DealerNeq::class, "dealer_neq_id");
    }

    public function spk()
    {
        return $this->belongsTo(Spk::class, "spk_id");
    }
}
