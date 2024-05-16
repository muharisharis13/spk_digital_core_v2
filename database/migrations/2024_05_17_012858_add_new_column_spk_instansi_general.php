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
            'spk_instansi_generals',
            function (Blueprint $table) {

                $table->string("province_id")->nullable();
                $table->string("city_id")->nullable();
                $table->string("district_id")->nullable();
                $table->string("sub_district_id")->nullable();
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
