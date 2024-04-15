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
        Schema::create('indents', function (Blueprint $table) {
            $table->uuid("indent_id")->primary();
            $table->uuid("dealer_id")->nullable();
            $table->foreign("dealer_id")->references("dealer_id")->on("dealer")->onDelete("set null");
            $table->uuid("motor_id")->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
            $table->uuid("color_id")->nullable();
            $table->foreign("color_id")->references("color_id")->on("colors")->onDelete("set null");
            $table->string("indent_people_name");
            $table->string("indent_nik")->nullable();
            $table->string("indent_wa_number")->nullable();
            $table->string("indent_phone_number")->nullable();
            $table->string("indent_type");
            $table->string("indent_status");
            $table->string("indent_note")->nullable();
            $table->integer("amount_total")->default(0);
            $table->uuid("sales_id")->nullable();
            $table->uuid("salesname")->nullable();
            // $table->foreign("sales_id")->references("sales_id")->on("sales")->onDelete("set null");
            $table->uuid("micro_finance_id")->nullable();
            $table->foreign("micro_finance_id")->references("micro_finance_id")->on("micro_finances")->onDelete("set null");
            $table->uuid("leasing_id")->nullable();
            $table->foreign("leasing_id")->references("leasing_id")->on("leasings")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indents');
    }
};
