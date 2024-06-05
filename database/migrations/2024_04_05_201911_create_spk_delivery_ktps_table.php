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
        Schema::create('spk_delivery_ktps', function (Blueprint $table) {
            $table->uuid("spk_delivery_ktp_id")->primary();
            $table->string("spk_delivery_ktp_customer_name");
            $table->string("spk_delivery_ktp_customer_address");
            $table->string("spk_delivery_ktp_city");
            $table->string("spk_delivery_ktp_no_telp")->nullable();
            $table->string("spk_delivery_ktp_no_phone");
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_delivery_ktps');
    }
};
