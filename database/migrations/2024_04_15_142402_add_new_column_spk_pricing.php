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
        Schema::table(
            'spk_pricings',
            function (Blueprint $table) {
                $table->uuid("broker_id")->nullable();
                $table->foreign("broker_id")->references("broker_id")->on("brokers")->onDelete("set null");
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
