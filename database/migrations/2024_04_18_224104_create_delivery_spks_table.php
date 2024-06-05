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
        Schema::create('delivery_spks', function (Blueprint $table) {
            $table->uuid("delivery_spk_id")->primary();
            $table->uuid("delivery_id")->nullable();
            $table->foreign("delivery_id")->references("delivery_id")->on("deliveries")->onDelete("set null");
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_spks');
    }
};
