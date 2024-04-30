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
            'retur_units',
            function (Blueprint $table) {
                $table->uuid("retur_unit_dealer_destination_id")->nullable();
                $table->string("retur_unit_dealer_destination_name")->nullable();
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
