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
        Schema::create('event_returns', function (Blueprint $table) {
            $table->uuid("event_return_id")->primary();
            $table->uuid("event_id")->nullable();
            $table->foreign("event_id")->references("event_id")->on("event")->onDelete()->onDelete("set null");
            $table->string("event_return_number")->unique();
            $table->string("event_return_status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_returns');
    }
};
