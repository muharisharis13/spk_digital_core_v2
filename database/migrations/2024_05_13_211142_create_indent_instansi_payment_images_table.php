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
        Schema::create('indent_instansi_payment_images', function (Blueprint $table) {
            $table->uuid("indent_instansi_payment_image_id")->primary();
            $table->uuid("idnt_instansi_payment_id")->nullable();
            $table->foreign("idnt_instansi_payment_id")->references("indent_instansi_payment_id")->on("indent_instansi_payments")->onDelete("set null");
            $table->string("image");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_instansi_payment_images');
    }
};
