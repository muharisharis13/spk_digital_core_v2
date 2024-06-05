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
        //
        Schema::table('repair_unit_lists', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);
            $table->dropForeign(['unit_id']);


            $table->uuid("repair_id")->nullable()->change();
            $table->foreign("repair_id")->references("repair_id")->on("repairs")->onDelete("set null");
            $table->uuid("unit_id")->nullable()->change();
            $table->foreign("unit_id")->references("unit_id")->on("unit")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
