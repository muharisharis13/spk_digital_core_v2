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
        Schema::create('unit', function (Blueprint $table) {
            $table->uuid("unit_id")->primary();
            $table->string("unit_color")->nullable();
            $table->string("unit_code");
            $table->string("unit_frame");
            $table->string("unit_engine");
            $table->date("unit_received_date")->nullable();
            $table->text("unit_note")->nullable();
            $table->string("unit_status")->nullable();
            $table->uuid("shipping_order_id")->nullable();
            $table->foreign("shipping_order_id")->references("shipping_order_id")->on("shipping_order");
            $table->uuid("event_id")->nullable();
            $table->foreign("event_id")->references("event_id")->on("event")->onDelete("set null");
            $table->uuid("motor_id")->nullable()->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
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
        Schema::dropIfExists('units');
    }
};
