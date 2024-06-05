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
        Schema::create('main_dealers', function (Blueprint $table) {
            $table->uuid("main_dealer_id")->primary();
            $table->string("main_dealer_name");
            $table->string("main_dealer_identifier");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_dealers');
    }
};
