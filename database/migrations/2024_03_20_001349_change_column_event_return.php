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

        Schema::table('event_returns', function (Blueprint $table) {
            $table->dropForeign(['event_id']);


            $table->uuid("master_event_id")->nullable();
            $table->foreign("master_event_id")->references("master_event_id")->on("master_events")->onDelete("set null");
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
