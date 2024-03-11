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
        Schema::create('event_logs', function (Blueprint $table) {
            $table->uuid("event_log_id")->primary();
            $table->uuid("event_id")->nullable();
            $table->foreign("event_id")->references("event_id")->on("event")->onDelete("set null");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("event_log_action");
            $table->string("event_log_note")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
