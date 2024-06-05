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
        Schema::create('spk_instansi_generals', function (Blueprint $table) {
            $table->uuid("spk_instansi_general_id")->primary();
            $table->uuid("spk_instansi_id")->nullable();
            $table->foreign("spk_instansi_id")->references("spk_instansi_id")->on("spk_instansis")->onDelete("set null");
            $table->string("sales_name");
            $table->string("sales_id");
            $table->string("po_no");
            $table->string("po_number");
            $table->date("po_date");
            $table->string("instansi_name");
            $table->string("instansi_address");
            $table->string("province");
            $table->string("city");
            $table->string("district");
            $table->string("sub_district");
            $table->string("postal_code")->nullable();
            $table->string("no_telp")->nullable();
            $table->string("no_hp");
            $table->string("email")->nullable();
            $table->bigInteger("po_values")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_instansi_generals');
    }
};
