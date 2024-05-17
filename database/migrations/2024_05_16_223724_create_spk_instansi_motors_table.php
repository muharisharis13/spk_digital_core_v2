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
        Schema::create('spk_instansi_motors', function (Blueprint $table) {
            $table->uuid("spk_instansi_motor_id")->primary();
            $table->uuid("spk_instansi_id")->nullable();
            $table->foreign("spk_instansi_id")->references("spk_instansi_id")->on("spk_instansis")->onDelete("set null");
            $table->uuid("motor_id")->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
            $table->uuid("color_id")->nullable();
            $table->foreign("color_id")->references("color_id")->on("colors")->onDelete("set null");
            $table->integer("qty");
            $table->bigInteger("off_the_road");
            $table->bigInteger("on_the_road");
            $table->bigInteger("bbn");
            $table->integer("discount");
            $table->integer("discount_over");
            $table->integer("commission");
            $table->integer("booster");
            $table->integer("additional_cost");
            $table->text("additional_cost_note");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_motors');
    }
};
