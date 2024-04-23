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
        Schema::create('spk_units', function (Blueprint $table) {
            $table->uuid("spk_unit_id")->primary();
            $table->uuid("motor_id")->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
            $table->uuid("color_id")->nullable();
            $table->foreign("color_id")->references("color_id")->on("colors")->onDelete("set null");
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->uuid("unit_id")->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit")->onDelete("set null");
            $table->string("spk_uniit_year")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_units');
    }
};
