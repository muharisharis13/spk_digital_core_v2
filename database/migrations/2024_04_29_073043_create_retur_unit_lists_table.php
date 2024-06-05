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
        Schema::create('retur_unit_lists', function (Blueprint $table) {
            $table->uuid("retur_unit_list_id")->primary();
            $table->uuid("retur_unit_id")->nullable();
            $table->foreign("retur_unit_id")->references("retur_unit_id")->on("retur_units")->onDelete("set null");
            $table->uuid("unit_id")->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit")->onDelete("set null");
            $table->enum("retur_unit_list_status", ["request", "approved", "reject"])->default("request");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_unit_lists');
    }
};
