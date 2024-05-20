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
        Schema::create('spk_instansi_payment_lists', function (Blueprint $table) {
            $table->uuid('spk_instansi_payment_list_id')->primary();
            $table->string("payment_list_number")->unique();
            $table->uuid("spk_instansi_payment_id")->nullable();
            $table->foreign("spk_instansi_payment_id")->references("spk_instansi_payment_id")->on("spk_instansi_payments")->onDelete("set null");
            $table->enum("payment_list_method", ["cash", "bank_transfer"]);
            $table->uuid("bank_id")->nullable();
            $table->foreign("bank_id")->references("bank_id")->on("banks")->onDelete("set null");
            $table->bigInteger("payment_list_amount");
            $table->date("payment_list_date");
            $table->text("payment_list_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_payment_lists');
    }
};
