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
        Schema::create('spk_ins_payment_list_imgs', function (Blueprint $table) {
            $table->uuid("spk_inst_payment_list_img_id")->primary();
            $table->uuid("spk_instansi_payment_list_id")->nullable();
            $table->foreign("spk_instansi_payment_list_id")->references("spk_instansi_payment_list_id")->on("spk_instansi_payment_lists")->onDelete("set null");
            $table->string("image");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_ins_payment_list_imgs');
    }
};
