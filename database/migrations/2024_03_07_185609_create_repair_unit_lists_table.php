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
        Schema::create('repair_unit_lists', function (Blueprint $table) {
            $table->uuid("repair_unit_list_id")->primary();
            $table->uuid("repair_id")->nullable();
            $table->foreign("repair_id")->references("repair_id")->on("repairs");
            $table->uuid("unit_id")->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_unit_lists');
    }
};
