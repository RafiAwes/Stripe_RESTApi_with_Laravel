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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id')->nullable();

            
            $table->unsignedBigInteger('supplier_id');

            
            $table->integer('amount'); // in cents
            $table->string('currency', 3)->default('usd');

            
            $table->integer('platform_fee')->default(0); // cents

            
            $table->enum('status', [
                'pending',       
                'paid',          
                'shipped',       
                'completed',     
                'cancelled'
            ])->default('pending');
            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
