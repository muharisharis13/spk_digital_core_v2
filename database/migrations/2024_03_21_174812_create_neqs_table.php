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
        Schema::create('neqs', function (Blueprint $table) {
            $table->uuid("neq_id")->primary();
            $table->string("neq_number")->unique();
            $table->date("neq_shipping_date");
            $table->uuid('dealer_neq_id')->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
            $table->uuid('dealer_id')->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->text("neq_note")->nullable();
            $table->string("neq_status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neqs');
    }
};
