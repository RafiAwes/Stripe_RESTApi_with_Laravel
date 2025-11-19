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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_id')->unique();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('oreder_id')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->integer('amount');
            $table->string('currency', 3);
            $table->enum ('status', ['pending', 'succeeded', 'failed'])->default('pending');
            $table->json('raw_response')->nullable();
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
