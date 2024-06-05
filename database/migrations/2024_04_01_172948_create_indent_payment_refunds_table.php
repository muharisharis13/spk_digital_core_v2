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
        Schema::create('indent_payment_refunds', function (Blueprint $table) {
            $table->uuid("indent_payment_refund_id")->primary();
            $table->integer("indent_payment_refund_amount_total");
            $table->uuid("indent_id")->nullable();
            $table->foreign("indent_id")->references("indent_id")->on("indents")->onDelete("set null");
            $table->text("indent_payment_refund_note");
            $table->string("indent_payment_refund_number")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_payment_refunds');
    }
};
