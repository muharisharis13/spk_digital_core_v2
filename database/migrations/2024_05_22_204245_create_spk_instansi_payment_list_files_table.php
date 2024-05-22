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
        Schema::create('spk_instansi_payment_list_files', function (Blueprint $table) {
            $table->uuid("uuid")->primary();
            $table->uuid("payment_list_id")->nullable();
            $table->foreign("payment_list_id")->references("spk_instansi_payment_list_id")->on("spk_instansi_payment_lists")->onDelete("set null");
            $table->string("file");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_payment_list_files');
    }
};
