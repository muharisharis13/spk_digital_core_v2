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
        Schema::create('spk_customers', function (Blueprint $table) {
            $table->uuid("spk_customer_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->string("spk_customer_nik");
            $table->string("spk_customer_name");
            $table->string("spk_customer_address");
            $table->uuid("province_id")->nullable();
            // $table->foreign("province_id")->references("province_id")->on("provinces")->onDelete("set null");
            $table->uuid("city_id")->nullable();
            // $table->foreign("city_id")->references("city_id")->on("cities")->onDelete("set null");
            $table->uuid("district_id")->nullable();
            // $table->foreign("district_id")->references("district_id")->on("districts")->onDelete("set null");
            $table->uuid("sub_district_id")->nullable();
            // $table->foreign("sub_district_id")->references("sub_district_id")->on("sub_districts")->onDelete("set null");
            $table->string("spk_customer_postal_code")->nullable();
            $table->string("spk_customer_birth_place");
            $table->date("spk_customer_birth_date");
            $table->enum("spk_customer_gender", ["man", "woman"]);
            $table->string("spk_customer_telp")->nullable();
            $table->string("spk_customer_no_wa")->nullable();
            $table->string("spk_customer_no_phone")->nullable();
            $table->string("spk_customer_religion")->nullable();
            $table->string("marital_name")->nullable();
            $table->uuid("marital_id")->nullable();
            $table->string("hobbies_name")->nullable();
            $table->uuid("hobbies_id")->nullable();
            $table->string("spk_customer_mother_name")->nullable();
            $table->string("spk_customer_npwp")->nullable();
            $table->string("spk_customer_email")->nullable();
            $table->string("residence_name")->nullable();
            $table->uuid("residence_id")->nullable();
            $table->string("education_name")->nullable();
            $table->uuid("education_id")->nullable();
            $table->string("work_name")->nullable();
            $table->uuid("work_id")->nullable();
            $table->string("spk_customer_length_of_work")->nullable();
            $table->string("spk_customer_income");
            $table->string("spk_customer_outcome");
            $table->string("motor_brand_name")->nullable();
            $table->uuid("motor_brand_id")->nullable();
            $table->string("spk_customer_motor_type_before")->nullable();
            $table->string("spk_customer_motor_year_before")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_customers');
    }
};
