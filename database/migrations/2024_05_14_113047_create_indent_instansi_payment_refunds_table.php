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
        Schema::create('indent_instansi_payment_refunds', function (Blueprint $table) {
            $table->uuid("indent_instansi_payment_refund_id")->primary();
            $table->string("refund_number")->unique();
            $table->uuid("indent_instansi_id")->nullable();
            $table->foreign("indent_instansi_id")->references("indent_instansi_id")->on("indent_instansis")->onDelete("Set null");
            $table->bigInteger("indent_instansi_payment_refund_total");
            $table->text("indent_instansi_payment_refund_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_instansi_payment_refunds');
    }
};
