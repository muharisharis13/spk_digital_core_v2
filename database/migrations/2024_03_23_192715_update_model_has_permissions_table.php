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
        Schema::table('model_has_permissions', function (Blueprint $table) {
            // Ubah tipe data kolom model_id menjadi UUID
            $table->uuid('model_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('model_has_permissions', function (Blueprint $table) {
            // Ubah kembali tipe data kolom model_id menjadi string (atau sesuai tipe data semula)
            $table->string('model_id')->change();
        });
    }
};
