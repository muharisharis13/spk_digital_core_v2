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
        Schema::create('indent_instansi_payments', function (Blueprint $table) {
            $table->uuid("indent_instansi_payment_id")->primary();
            $table->uuid("indent_instansi_id")->nullable();
            $table->foreign("indent_instansi_id")->references("indent_instansi_id")->on("indent_instansis")->onDelete("set null");
            $table->enum("indent_instansi_payment_method", ["cash", "giro", "bank_transfer"]);
            $table->uuid("bank_id")->nullable();
            $table->foreign("bank_id")->references("bank_id")->on("banks")->onDelete("set null");
            $table->integer("indent_instansi_payment_amount")->default(0);
            $table->date("indent_instansi_payment_date");
            $table->text("indent_instansi_payment_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_instansi_payments');
    }
};
