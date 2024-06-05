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
        Schema::create('spk_instansi_refund_payments', function (Blueprint $table) {
            $table->uuid("spk_instansi_refund_payment_id")->primary();
            $table->uuid("spk_instansi_payment_id")->nullable();
            $table->foreign("spk_instansi_payment_id")->references("spk_instansi_payment_id")->on("spk_instansi_payments")->onDelete("set null");
            $table->bigInteger("amount_total");
            $table->text("note");

            $table->string("number")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_refund_payments');
    }
};
