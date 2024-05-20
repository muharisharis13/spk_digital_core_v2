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
        Schema::create('spk_instansi_payment_logs', function (Blueprint $table) {
            $table->uuid("spk_instansi_payment_log_id")->primary();
            $table->uuid("spk_instansi_payment_id")->nullable();
            $table->foreign("spk_instansi_payment_id")->references("spk_instansi_payment_id")->on("spk_instansi_payments")->onDelete("set null");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->text("spk_instansi_payment_log_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_payment_logs');
    }
};
