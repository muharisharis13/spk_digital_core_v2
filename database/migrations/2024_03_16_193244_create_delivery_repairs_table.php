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
        Schema::create('delivery_repairs', function (Blueprint $table) {
            $table->uuid("delivery_repair_id")->primary();
            $table->uuid('delivery_id')->nullable();
            $table->foreign("delivery_id")->references("delivery_id")->on("deliveries")->onDelete("set null");
            $table->uuid('repair_id')->nullable();
            $table->foreign("repair_id")->references("repair_id")->on("repairs")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_repairs');
    }
};
