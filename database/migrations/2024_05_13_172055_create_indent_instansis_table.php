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
        Schema::create('indent_instansis', function (Blueprint $table) {
            $table->uuid("indent_instansi_id")->primary();
            $table->enum("indent_instansi_status", ["create", "finance_check", "cashier_check", "unpaid", "paid", "spk", "cancel"])->default("create");
            $table->uuid("sales_id")->nullable();
            $table->string("salesman_name")->nullable();
            $table->integer("indent_instansi_nominal")->default(0);
            $table->date("indent_instansi_date");
            $table->string("indent_instansi_number_po");
            $table->date("indent_instansi_po_date");
            $table->string("indent_instansi_name");
            $table->string("indent_instansi_address");
            $table->uuid("province_id");
            $table->string("province_name");
            $table->uuid("city_id");
            $table->string("city_name");
            $table->uuid("district_id");
            $table->string("district_name");
            $table->uuid("sub_district_id");
            $table->string("sub_district_name");
            $table->string("indent_instansi_postal_code")->nullable();
            $table->string("indent_instansi_no_telp")->nullable();
            $table->string("indent_instansi_no_hp");
            $table->string("indent_instansi_email")->nullable();
            $table->uuid("motor_id")->nullable();
            $table->foreign("motor_id")->references("motor_id")->on("motor")->onDelete("set null");
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
        Schema::dropIfExists('indent_instansis');
    }
};
