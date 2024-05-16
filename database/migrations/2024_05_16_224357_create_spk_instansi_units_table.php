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
        Schema::create('spk_instansi_units', function (Blueprint $table) {
            $table->uuid("spk_instansi_unit_id")->primary();
            $table->uuid("spk_instansi_motor_id")->nullable();
            $table->foreign("spk_instansi_motor_id")->references("spk_instansi_motor_id")->on("spk_instansi_motors")->onDelete("set null");
            $table->uuid("unit_id")->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit")->onDelete("set null");
            $table->boolean("is_have_legal")->default(false);
            $table->boolean("is_delivery_partial")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_units');
    }
};
