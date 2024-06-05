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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->uuid("delivery_id")->primary();
            $table->string("delivery_driver_name");
            $table->string("delivery_vehicle");
            $table->text("delivery_completeness")->nullable();
            $table->text("delivery_note")->nullable();
            $table->string("delivery_number")->unique();
            $table->string("delivery_type");
            $table->uuid("repair_id")->nullable();
            $table->foreign("repair_id")->references("repair_id")->on("repairs")->onDelete("set null");
            $table->string("delivery_status");
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
        Schema::dropIfExists('deliveries');
    }
};
