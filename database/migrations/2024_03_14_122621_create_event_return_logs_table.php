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
        Schema::create('event_return_logs', function (Blueprint $table) {
            $table->uuid("event_return_log_id")->primary();
            $table->uuid("event_return_id")->nullable();
            $table->foreign("event_return_id")->references("event_return_id")->on("event_returns")->onDelete("set null");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("event_return_log_action")->nullable();
            $table->string("event_return_log_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_return_logs');
    }
};
