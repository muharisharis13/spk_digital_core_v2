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
        Schema::create('neq_returns', function (Blueprint $table) {
            $table->uuid("neq_return_id")->primary();
            $table->uuid("neq_id")->nullable();
            $table->foreign("neq_id")->references("neq_id")->on("neqs")->onDelete("set null");
            $table->string("neq_return_number")->unique();
            $table->string("neq_return_status");
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neq_returns');
    }
};
