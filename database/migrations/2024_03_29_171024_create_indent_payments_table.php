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
        Schema::create('indent_payments', function (Blueprint $table) {
            $table->uuid("indent_payment_id")->primary();
            $table->uuid("indent_id")->nullable();
            $table->foreign("indent_id")->references("indent_id")->on("indents")->onDelete("set null");
            $table->string("indent_payment_method");
            $table->uuid("bank_id")->nullable();
            $table->foreign("bank_id")->references("bank_id")->on("banks")->onDelete("set null");
            $table->integer("indent_payment_amount")->default(0);
            $table->date("indent_payment_date");
            $table->string("indent_payment_note")->nullable();
            $table->string("indent_payment_img")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_payments');
    }
};
