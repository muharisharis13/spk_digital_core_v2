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
        Schema::create('neq_return_logs', function (Blueprint $table) {
            $table->uuid("neq_return_log_id")->primary();
            $table->uuid("neq_return_id")->nullable();
            $table->foreign("neq_return_id")->references("neq_return_id")->on("neq_returns")->onDelete("set null");
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("neq_return_log_action")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neq_return_logs');
    }
};
