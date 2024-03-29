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


        Schema::table('indent_logs', function (Blueprint $table) {

            $table->string("indent_id")->nullable();
            $table->foreign("indent_id")->references("indent_id")->on("indents")->onDelete("set null");
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
