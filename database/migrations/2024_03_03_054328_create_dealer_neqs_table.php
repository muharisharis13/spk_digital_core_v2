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
        Schema::create('dealer_neq', function (Blueprint $table) {
            $table->uuid("dealer_neq_id")->primary();
            $table->string("dealer_neq_name");
            $table->string("dealer_neq_address");
            $table->string("dealer_neq_phone_number");
            $table->string("dealer_neq_code");
            $table->string("dealer_neq_city")->nullable();
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealer_neqs');
    }
};
