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
        Schema::create('spk_payment_logs', function (Blueprint $table) {
            $table->uuid("spk_payment_log_id")->primary();
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->text("spk_payment_log_action")->nullable();
            $table->uuid("spk_payment_id")->nullable();
            $table->foreign("spk_payment_id")->references("spk_payment_id")->on("spk_payments")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_payment_logs');
    }
};
