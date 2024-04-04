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
        Schema::create('pricelist_motors', function (Blueprint $table) {
            $table->uuid("pricelist_motor_id")->primary();
            $table->uuid("motor_id")->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
            $table->integer("off_the_road");
            $table->integer("bbn");
            $table->integer("commission")->default(0);
            $table->enum("pricelist_location_type", ["dealer", "neq"]);
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_motors');
    }
};
