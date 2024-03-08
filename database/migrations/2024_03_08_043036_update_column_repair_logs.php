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
        Schema::table('repair_logs', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);
            $table->dropForeign(['user_id']);


            $table->uuid("repair_id")->nullable()->change();
            $table->foreign("repair_id")->references("repair_id")->on("repairs")->onDelete("set null");
            $table->uuid("user_id")->nullable()->change();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //


        Schema::table('repair_logs', function (Blueprint $table) {
            $table->dropForeign(['repair_id']);
            $table->dropForeign(['user_id']);


            $table->uuid("repair_id")->nullable()->change();
            $table->foreign("repair_id")->references("repair_id")->on("repairs")->onDelete("set null");
            $table->uuid("user_id")->nullable()->change();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
        });
    }
};
