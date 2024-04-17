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
        Schema::create('spk_purchase_order_files', function (Blueprint $table) {
            $table->uuid("spk_purchase_order_file_id")->primary();
            $table->uuid("spk_purchase_order_id")->nullable();
            $table->foreign("spk_purchase_order_id")->references("spk_purchase_order_id")->on("spk_purchase_orders")->onDelete("set null");
            $table->string("spk_purchase_order_file_path")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_purchase_order_files');
    }
};
