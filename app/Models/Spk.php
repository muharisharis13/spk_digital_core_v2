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
    protected $with = ["spk_log", "spk_legal", "spk_pricing", "spk_unit.motor", "spk_unit.unit", "spk_transaction", "spk_general.indent", "spk_general.sales", "spk_general.dealer", "spk_general.dealer_neq", "spk_customer", "spk_additional_document", "spk_additional_document_another", "spk_pricing_accecories", "spk_pricing_additional", "spk_delivery_ktp", "spk_delivery_dealer_neq", "spk_delivery_dealer", "spk_delivery_domicile"];

    public function spk_delivery_domicile()
    {
        return $this->hasOne(SpkDeliveryDomicile::class, "spk_id");
    }
    public function spk_delivery_dealer()
    {
        return $this->hasOne(SpkDeliveryDealer::class, "spk_id");
    }
    public function spk_delivery_dealer_neq()
    {
        return $this->hasOne(SpkDeliveryNeq::class, "spk_id");
    }
    public function spk_delivery_ktp()
    {
        return $this->hasOne(SpkDeliveryKtp::class, "spk_id");
    }

    public function spk_pricing_additional()
    {
        return $this->hasMany(SpkPricingAdditional::class, "spk_id");
    }
    public function spk_pricing_accecories()
    {
        return $this->hasMany(SpkPricingAccecories::class, "spk_id");
    }
    public function spk_additional_document_another()
    {
        return $this->hasMany(SpkAdditionalDocumentAnother::class, "spk_id");
    }
    public function spk_additional_document()
    {
        return $this->hasOne(SpkAdditionalDocument::class, "spk_id");
    }
    public function spk_customer()
    {
        return $this->hasOne(SpkCustomer::class, "spk_id");
    }
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

    public function spk_log()
    {
        return $this->hasMany(SpkLog::class, "spk_id");
    }
}
