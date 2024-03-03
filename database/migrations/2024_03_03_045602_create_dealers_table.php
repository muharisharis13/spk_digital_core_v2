<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dealer', function (Blueprint $table) {
            $table->uuid("dealer_id")->primary();
            $table->string("dealer_name");
            $table->text("dealer_address")->nullable();
            $table->string("dealer_phone_number")->nullable();
            $table->string("dealer_code");
            $table->string("dealer_city")->nullable();
            $table->string("dealer_type");
            $table->string("dealer_location_alias")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer');
    }
};
