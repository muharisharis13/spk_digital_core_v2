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
        Schema::create('spk_purchase_orders', function (Blueprint $table) {
            $table->uuid("spk_purchase_order_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->date("spk_purchase_order_date");
            $table->string("spk_purchase_order_no");
            $table->enum("spk_purchase_order_type", ["cash", "credit"]);
            $table->integer("spk_purchase_order_tac");
            $table->integer("spk_purchase_order_act_tac");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_purchase_orders');
    }
};
