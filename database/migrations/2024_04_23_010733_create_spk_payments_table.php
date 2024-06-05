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
        Schema::create('spk_payments', function (Blueprint $table) {
            $table->uuid("spk_payment_id")->primary();
            $table->string("spk_payment_number")->unique();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->enum("spk_payment_for", ["customer", "leasing"]);
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->enum("spk_payment_type", ["cash", "leasing", "dp"]);
            $table->enum("spk_payment_status", ["unpaid", "paid", "cancel", "cashier_check", "finance_check"])->default("unpaid");
            // $table->integer("spk_payment_amount_total");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_payments');
    }
};
