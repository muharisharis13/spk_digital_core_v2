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
        Schema::create('spk_generals', function (Blueprint $table) {
            $table->uuid("spk_general_id")->primary();
            $table->uuid("indent_id")->nullable();
            $table->foreign("indent_id")->references("indent_id")->on("indents")->onDelete("set null");
            $table->date("spk_general_indent_date");
            $table->uuid("sales_id")->nullable();
            $table->foreign("sales_id")->references("sales_id")->on("sales")->onDelete("set null");
            $table->string("spk_general_method_sales");
            $table->enum("spk_general_location", ["dealer", "neq"]);
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->uuid("dealer_neq_id")->nullable();
            $table->foreign("dealer_neq_id")->references("dealer_neq_id")->on("dealer_neq")->onDelete("set null");
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_generals');
    }
};
