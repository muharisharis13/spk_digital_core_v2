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
        Schema::create('spk_payment_lists', function (Blueprint $table) {
            $table->uuid("spk_payment_list_id")->primary();
            $table->uuid("spk_payment_id")->nullable();
            $table->foreign("spk_payment_id")->references("spk_payment_id")->on("spk_payments")->onDelete("set null");
            $table->enum("spk_payment_list_method", ["cash", "bank_transfer", "giro"]);
            $table->uuid("bank_id")->nullable();
            $table->foreign("bank_id")->references("bank_id")->on("banks")->onDelete("set null");
            $table->integer("spk_payment_list_amount");
            $table->date("spk_payment_list_date");
            $table->text("spk_payment_list_note")->nullable();
            $table->string("spk_payment_list_number")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_payment_lists');
    }
};
