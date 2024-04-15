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

        Schema::table('spk_customers', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['city_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['sub_district_id']);

            // $table->dropColumn(["province_id"]);
            // $table->dropColumn(["city_id"]);
            // $table->dropColumn(["district_id"]);
            // $table->dropColumn(["sub_district_id"]);


            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("sub_district")->nullable();
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
