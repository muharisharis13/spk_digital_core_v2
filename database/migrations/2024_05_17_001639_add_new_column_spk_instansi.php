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
        //

        Schema::table(
            'spk_instansis',
            function (Blueprint $table) {

                $table->uuid("indent_instansi_id")->nullable();
                $table->foreign("indent_instansi_id")->references("indent_instansi_id")->on("indent_instansis")->onDelete("set null");
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
