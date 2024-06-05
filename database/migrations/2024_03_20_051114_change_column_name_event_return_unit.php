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
        Schema::table('event_return_list_units', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn(["unit_id"]);


            $table->uuid("event_list_unit_id")->nullable();
            $table->foreign("event_list_unit_id")->references("event_list_unit_id")->on("event_list_units")->onDelete("set null");
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
