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
        Schema::create('neq_logs', function (Blueprint $table) {
            $table->uuid("neq_log_id")->primary();
            $table->uuid('neq_id')->nullable();
            $table->foreign("neq_id")->references("neq_id")->on("neqs")->onDelete("set null");
            $table->uuid('user_id')->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("neq_log_action")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neq_logs');
    }
};
