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
        Schema::create('spk_instansi_payments', function (Blueprint $table) {
            $table->uuid("spk_instansi_payment_id")->primary();
            $table->uuid("spk_instansi_id")->nullable();
            $table->foreign("spk_instansi_id")->references("spk_instansi_id")->on("spk_instansis")->onDelete("set null");
            $table->string("spk_instansi_payment_number")->unique();
            $table->enum("spk_instansi_payment_for", ["customer", "leasing", "company"]);
            $table->enum("spk_instansi_payment_type", ["cash"]);
            $table->enum("spk_instansi_payment_status", ["unpaid", "paid", "cancel", "cashier_check", "finance_check"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_payments');
    }
};
