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
        Schema::create('spk_payment_list_refunds', function (Blueprint $table) {
            $table->uuid("spk_payment_list_refund_id")->primary();
            $table->uuid("spk_payment_id")->nullable();
            $table->foreign("spk_payment_id")->references("spk_payment_id")->on("spk_payments")->onDelete("set null");
            $table->integer("spk_payment_list_refund_amount_total");
            $table->text("spk_payment_list_refund_note");
            $table->string("spk_payment_list_refund_number")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_payment_list_refunds');
    }
};
