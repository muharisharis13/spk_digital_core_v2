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
        Schema::create('repairs', function (Blueprint $table) {
            $table->uuid("repair_id")->primary();
            $table->uuid("main_dealer_id")->nullable();
            $table->foreign("main_dealer_id")->references("main_dealer_id")->on("main_dealers")->onDelete("set null");
            $table->text("repair_reason")->nullable();
            $table->string("repair_status");
            $table->string("repair_number")->unique();
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
        Schema::dropIfExists('repairs');
    }
};
