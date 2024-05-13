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
        Schema::create('indent_instansi_logs', function (Blueprint $table) {
            $table->uuid("indent_instansi_log_id")->primary();
            $table->uuid("user_id")->nullable();
            $table->foreign("user_id")->references("user_id")->on("users")->onDelete("set null");
            $table->string("indent_instansi_log_action")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indent_instansi_logs');
    }
};
