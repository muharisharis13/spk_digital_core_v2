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
        Schema::create('unit_logs', function (Blueprint $table) {
            $table->uuid("unit_log_id")->primary();
            $table->uuid("unit_id")->nullable();
            $table->foreign("unit_id")->references("unit_id")->on("unit");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users");
            $table->string("unit_log_number");
            $table->string("unit_log_dealer_name")->nullable();
            $table->string("unit_log_dealer_neq_name")->nullable();
            $table->string("unit_log_event_name")->nullable();
            $table->string("unit_log_action");
            $table->string("unit_log_status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_logs');
    }
};
