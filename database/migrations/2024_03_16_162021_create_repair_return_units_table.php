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
        Schema::create('repair_return_units', function (Blueprint $table) {
            $table->uuid("repair_return_unit_id")->primary();
            $table->uuid("repair_return_id")->nullable();
            $table->foreign("repair_return_id")->references("repair_return_id")->on("repair_returns")->onDelete("set null");
            $table->uuid("repair_unit_list_id")->nullable();
            $table->foreign("repair_unit_list_id")->references("repair_unit_list_id")->on("repair_unit_lists")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_return_units');
    }
};
