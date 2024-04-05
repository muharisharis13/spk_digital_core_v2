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
        Schema::create('spk_legals', function (Blueprint $table) {
            $table->uuid("spk_legal_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->string("spk_legal_nik");
            $table->string("spk_legal_name");
            $table->string("spk_legal_address");
            $table->string("province")->nullable();
            $table->string("city")->nullable();
            $table->string("district")->nullable();
            $table->string("sub_district")->nullable();
            $table->string("spk_legal_postal_code")->nullable();
            $table->string("spk_legal_birth_place");
            $table->date("spk_legal_birth_date");
            $table->enum("spk_legal_gender", ["man", "woman"]);
            $table->string("spk_legal_telp")->nullable();
            $table->string("spk_legal_no_phone")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_legals');
    }
};
