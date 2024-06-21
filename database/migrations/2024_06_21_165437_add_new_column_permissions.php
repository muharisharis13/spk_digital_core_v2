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

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('alias_name')->nullable(); // Added nullable to handle existing records
            $table->string('group_name')->nullable(); // Added nullable to handle existing records
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
