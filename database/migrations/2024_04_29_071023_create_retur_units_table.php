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
        Schema::create('retur_units', function (Blueprint $table) {
            $table->uuid("retur_unit_id")->primary();
            $table->enum("retur_unit_status", ["create", "confirm", "approved", "reject"]);
            $table->enum("dealer_type", ["mds", "independent"])->nullable();
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->text("retur_unit_reason")->nullable();
            $table->string("main_dealer_name")->nullable();
            $table->string("main_dealer_id");
            $table->string("retur_unit_number")->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retur_units');
    }
};
