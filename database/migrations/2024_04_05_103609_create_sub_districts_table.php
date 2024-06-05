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
        Schema::create('sub_districts', function (Blueprint $table) {
            $table->uuid("sub_district_id")->primary();
            $table->string("sub_district_name");
            $table->uuid("district_id")->nullable();
            $table->foreign("district_id")->references("district_id")->on("districts")->onDelete("set null");
            $table->string("sub_disctrict_code")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_districts');
    }
};
