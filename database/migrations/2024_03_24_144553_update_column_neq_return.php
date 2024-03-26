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
        Schema::table('neq_returns', function (Blueprint $table) {
            $table->dropForeign(['neq_id']);
            $table->dropColumn(["neq_id"]);


            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
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
