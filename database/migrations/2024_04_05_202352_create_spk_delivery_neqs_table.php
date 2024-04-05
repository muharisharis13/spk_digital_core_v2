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
        Schema::create('spk_delivery_neqs', function (Blueprint $table) {
            $table->uuid("spk_delivery_neq_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
            $table->string("dealer_delivery_neq_customer_name");
            $table->string("dealer_delivery_neq_customer_no_phone");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_delivery_neqs');
    }
};
