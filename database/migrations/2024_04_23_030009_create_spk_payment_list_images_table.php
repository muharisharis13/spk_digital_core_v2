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
        Schema::create('spk_payment_list_images', function (Blueprint $table) {
            $table->uuid("spk_payment_list_image_id")->primary();
            $table->uuid("spk_payment_list_id")->nullable();
            $table->foreign("spk_payment_list_id")->references("spk_payment_list_id")->on("spk_payment_lists")->onDelete("set null");
            $table->string("spk_payment_list_img");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_payment_list_images');
    }
};
