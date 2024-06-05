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
        Schema::create('neq_units', function (Blueprint $table) {
            $table->uuid("neq_unit_id")->primary();
            $table->uuid('neq_id')->nullable();
            $table->foreign("neq_id")->references("neq_id")->on("neqs")->onDelete("set null");
            $table->uuid('unit_id')->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit")->onDelete("set null");
            $table->boolean("is_return")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neq_units');
    }
};
