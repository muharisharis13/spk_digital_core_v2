<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('spk_pricings', function (Blueprint $table) {
            $table->string("spk_pricing_on_the_road_note")->nullable();
            $table->string("spk_pricing_indent_note")->nullable();
            $table->string("spk_pricing_discount_note")->nullable();
            $table->string("spk_pricing_subsidi_note")->nullable();
            $table->string("spk_pricing_booster_note")->nullable();
            $table->string("spk_pricing_commission_note")->nullable();
            $table->string("spk_pricing_surveyor_commission_note")->nullable();
            $table->string("spk_pricing_broker_note")->nullable();
            $table->string("spk_pricing_broker_commission_note")->nullable();
            $table->string("spk_pricing_cashback_note")->nullable();
            $table->string("spk_pricing_delivery_cost_note")->nullable();
            $table->string("spk_pricing_over_delivery_note")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
