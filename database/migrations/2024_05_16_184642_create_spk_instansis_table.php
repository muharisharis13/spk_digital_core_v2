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
        Schema::create('spk_instansis', function (Blueprint $table) {
            $table->uuid("spk_instansi_id")->primary();
            $table->string("spk_instansi_number")->unique();
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->enum("spk_instansi_status", ["create", "finance_check", "shipment", "spk", "publish", "cancel"]);
            $table->boolean("is_cro_check")->default(false)->nullable();
            $table->text("spk_instansi_cro_check_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansis');
    }
};
