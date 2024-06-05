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
        Schema::create('delivery_neq_returns', function (Blueprint $table) {
            $table->uuid("delivery_neq_return_id")->primary();
            $table->uuid("delivery_id")->nullable();
            $table->foreign("delivery_id")->references("delivery_id")->on("deliveries")->onDelete("set null");
            $table->uuid("neq_return_id")->nullable();
            $table->foreign("neq_return_id")->references("neq_return_id")->on("neq_returns")->onDelete("set null");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_neq_returns');
    }
};
