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
        Schema::create('retur_unit_logs', function (Blueprint $table) {
            $table->uuid("retur_unit_log_id")->primary();
            $table->uuid("retur_unit_id")->nullable();
            $table->foreign("retur_unit_id")->references("retur_unit_id")->on("retur_units")->onDelete("set null");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("retur_unit_log_action");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_unit_logs');
    }
};
