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
        Schema::create('spk_transactions', function (Blueprint $table) {
            $table->uuid("spk_transaction_id")->primary();
            $table->uuid("spk_id")->nullable();
            $table->foreign("spk_id")->references("spk_id")->on("spks")->onDelete("set null");
            $table->enum("spk_transaction_method_buying", ["on_the_road", "off_the_road"]);
            $table->enum("spk_transaction_method_payment", ["cash", "credit"]);
            $table->uuid("leasing_name")->nullable();
            // $table->foreign("leasing_id")->references("leasing_id")->on("leasings")->onDelete("set null");
            $table->integer("spk_transaction_down_payment")->nullable();
            $table->string("spk_transaction_tenor")->nullable();
            $table->string("spk_transaction_instalment")->nullable();
            $table->string("spk_transaction_surveyor_name");
            $table->uuid("microfinance_name")->nullable();
            // $table->foreign("micro_finance_id")->references("micro_finance_id")->on("micro_finances")->onDelete("set null");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spk_transactions');
    }
};
