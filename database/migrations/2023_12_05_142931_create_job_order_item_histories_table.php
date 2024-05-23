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
        Schema::create('job_order_item_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jobOrderID')->constrained('job_orders');
            $table->foreignId('itemID')->constrained('inventories');
            $table->float('quantityUsed', 7, 3);
            $table->float('amountPaid', 7, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_item_histories');
    }
};
