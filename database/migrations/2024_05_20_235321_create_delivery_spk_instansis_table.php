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
        Schema::create('delivery_spk_instansis', function (Blueprint $table) {
            $table->uuid("delivery_spk_instansi_id")->primary();
            $table->uuid("delivery_id")->nullable();
            $table->foreign("delivery_id")->references("delivery_id")->on("deliveries")->onDelete("set null");
            $table->uuid("spk_instansi_id")->nullable();
            $table->foreign("spk_instansi_id")->references("spk_instansi_id")->on("spk_instansis")->onDelete("set null");
            $table->uuid("spk_instansi_unit_delivery_id")->nullable();
            $table->foreign("spk_instansi_unit_delivery_id")->references("spk_instansi_unit_delivery_id")->on("spk_instansi_unit_deliveries")->onDelete("set null");
            $table->enum("type", ["dc", "partial"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_spk_instansis');
    }
};
