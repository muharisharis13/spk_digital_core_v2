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
        Schema::create('spk_instansi_unit_deliv_files', function (Blueprint $table) {
            $table->uuid("spk_instansi_unit_deliv_file_id")->primary();
            $table->uuid("spk_instansi_unit_deliv_id")->nullable();
            $table->foreign("spk_instansi_unit_deliv_id")->references("spk_instansi_unit_delivery_id")->on("spk_instansi_unit_deliveries")->onDelete("set null");
            $table->string("file");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_unit_deliv_files');
    }
};
