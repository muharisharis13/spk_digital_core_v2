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
        Schema::create('motor_brands', function (Blueprint $table) {
            $table->uuid("motor_brand_id")->primary();
            $table->string("motor_brand_name");
            $table->enum("motor_brand_status", ["active", "unactive"])->default("active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_brands');
    }
};
