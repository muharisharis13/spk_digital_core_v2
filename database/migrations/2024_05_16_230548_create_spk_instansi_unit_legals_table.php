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
        Schema::create('spk_instansi_unit_legals', function (Blueprint $table) {
            $table->uuid("spk_instansi_unit_legal_id")->primary();
            $table->uuid("spk_instansi_unit_id")->nullable();
            $table->foreign("spk_instansi_unit_id")->references("spk_instansi_unit_id")->on("spk_instansi_units")->onDelete("set null");


            $table->string("instansi_name");
            $table->string("instansi_address");
            $table->string("province");
            $table->string("city");
            $table->string("district");
            $table->string("sub_district");
            $table->string("postal_code");
            $table->string("no_telp")->nullable();
            $table->string("no_hp");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_unit_legals');
    }
};
