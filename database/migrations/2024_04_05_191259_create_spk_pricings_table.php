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
        Schema::create('spk_pricings', function (Blueprint $table) {
            $table->uuid("spk_pricing_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->integer("spk_pricing_off_the_road");
            $table->integer("spk_pricing_bbn");
            $table->integer("spk_pricing_on_the_road");
            $table->integer("spk_pricing_indent_nominal")->nullable();
            $table->integer("spk_pricing_discount")->nullable();
            $table->integer("spk_pricing_subsidi")->nullable();
            $table->integer("spk_pricing_booster")->nullable();
            $table->integer("spk_pricing_commission")->nullable();
            $table->integer("spk_pricing_commission_surveyor")->nullable();
            $table->string("spk_pricing_broker_name")->nullable();
            $table->integer("spk_pricing_broker_commission")->nullable();
            $table->integer("spk_pricing_cashback")->nullable();
            $table->integer("spk_pricing_delivery_cost")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_pricings');
    }
};
