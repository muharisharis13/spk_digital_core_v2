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
        Schema::create('spk_delivery_file_sks', function (Blueprint $table) {
            $table->uuid("spk_delivery_file_sk_id")->primary();
            $table->uuid("spk_delivery_domicile_id")->nullable();
            $table->foreign("spk_delivery_domicile_id")->references("spk_delivery_domicile_id")->on("spk_delivery_domiciles")->onDelete("set null");
            $table->string("file")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_delivery_file_sks');
    }
};
