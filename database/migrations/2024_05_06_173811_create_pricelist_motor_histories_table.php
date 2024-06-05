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
        Schema::create('pricelist_motor_histories', function (Blueprint $table) {
            $table->uuid("pricelist_motor_histories_id")->primary();
            $table->uuid("pricelist_motor_id")->nullable();
            $table->foreign("pricelist_motor_id")->references("pricelist_motor_id")->on("pricelist_motors")->onDelete("set null");
            $table->integer("off_the_road");
            $table->integer("bbn");
            $table->integer("commission");
            $table->integer("discount");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_motor_histories');
    }
};
