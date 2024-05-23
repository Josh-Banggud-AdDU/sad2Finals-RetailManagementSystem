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
        Schema::create('job_order_services', function (Blueprint $table) {
            $table->foreignId('jobOrderID')->default(1)->constrained('job_orders');
            $table->foreignId('serviceID')->default(1)->constrained('services');
            $table->float('amountPaid', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_services');
    }
};
