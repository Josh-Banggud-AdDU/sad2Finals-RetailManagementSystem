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
        Schema::create('job_order_service_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jobOrderID')->constrained('job_orders');
            $table->foreignId('serviceID')->constrained('services');
            $table->float('amountPaid', 7, 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_service_histories');
    }
};
