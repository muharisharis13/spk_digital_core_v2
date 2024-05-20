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
        Schema::create('spk_instansi_unit_deliveries', function (Blueprint $table) {
            $table->uuid("spk_instansi_unit_delivery_id")->primary();
            $table->uuid("spk_instansi_unit_id")->nullable();
            $table->foreign("spk_instansi_unit_id")->references("spk_instansi_unit_id")->on("spk_instansi_units")->onDelete("set null");
            $table->enum("delivery_type", ["ktp", "dealer", "neq", "domicile"]);
            $table->string("name");
            $table->string("address");
            $table->string("city")->nullable();
            $table->string("no_telp")->nullable();
            $table->string("no_hp");
            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
            $table->boolean("is_domicile")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_unit_deliveries');
    }
};
