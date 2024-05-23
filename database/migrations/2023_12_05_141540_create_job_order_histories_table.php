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
        Schema::create('job_order_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jobOrderID')->constrained('job_orders')->default(1);
            $table->string('jobOrderStatus', 10)->default('Ongoing');
            $table->string('paymentStatus', 25)->default('Unpaid');
            $table->float('total',8,2)->default(0.00);
            $table->float('amountDue',8,2)->default(0.00);
            $table->float('profit',8,2)->default(0.00);
            $table->date('jobOrderDate');
            $table->text('vehicleDetails')->default('');
            $table->text('generalNotes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_order_histories');
    }
};
