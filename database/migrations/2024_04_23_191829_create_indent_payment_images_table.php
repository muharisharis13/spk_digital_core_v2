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
        Schema::create('indent_payment_images', function (Blueprint $table) {
            $table->uuid("indent_payment_image_id")->primary();
            $table->uuid("indent_payment_id")->nullable();
            $table->foreign("indent_payment_id")->references("indent_payment_id")->on("indent_payments")->onDelete("set null");
            $table->string("indent_payment_img");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_payment_images');
    }
};
