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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itemID')->default(1)->constrained('inventories');
            $table->char('itemSerialNumber', 30);
            $table->string('itemName', 255);
            $table->string('itemCategory', 255);
            $table->string('itemModel', 255);
            $table->string('itemLocation', 255);
            $table->float('itemQuantity', 7, 3)->default('0.000');
            $table->char('itemUnit', 10);
            $table->float('itemThreshold', 7, 3);
            $table->float('itemCostPrice', 8, 2);
            $table->float('itemSRP', 8, 2);
            $table->float('itemMarkup', 5, 5)->nullable();
            $table->boolean('isNotifying')->default(0);
            $table->boolean('isDisabled')->default(0);
            $table->foreignId('supplierID')->default(1)->constrained('suppliers');
            $table->text('generalNotes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};