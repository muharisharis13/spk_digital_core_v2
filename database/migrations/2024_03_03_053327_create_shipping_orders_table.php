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
        Schema::create('shipping_order', function (Blueprint $table) {
            $table->uuid("shipping_order_id")->primary();
            $table->string("shipping_order_number");
            $table->string("shipping_order_delivery_number");
            $table->string("shipping_order_status");
            $table->date("shipping_order_shipping_date");
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_orders');
    }
};
