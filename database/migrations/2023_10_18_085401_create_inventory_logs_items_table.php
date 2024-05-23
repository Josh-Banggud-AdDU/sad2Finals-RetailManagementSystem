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
        Schema::create('inventory_logs_items', function (Blueprint $table) {
            $table->foreignId('inventory_logID')->references('id')->on('inventory_logs');
            $table->foreignId('itemID')->references('id')->on('inventories');
            $table->float('quantity', 7, 3);
            $table->string('supplierName', 255)->nullable();
            $table->text('generalNotes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs_items');
    }
};
