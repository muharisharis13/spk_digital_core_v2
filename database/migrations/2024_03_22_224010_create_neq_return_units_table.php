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
        Schema::create('neq_return_units', function (Blueprint $table) {
            $table->uuid("neq_return_unit_id")->primary();
            $table->uuid("neq_return_id")->nullable();
            $table->foreign("neq_return_id")->references("neq_return_id")->on("neq_returns")->onDelete("set null");
            $table->uuid("neq_unit_id")->nullable();
            $table->foreign("neq_unit_id")->references("neq_unit_id")->on("neq_units")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neq_return_units');
    }
};
