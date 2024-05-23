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
        Schema::create('cash_on_hand_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_on_hand_id')->constrained('cash_on_hands');
            $table->integer('thousands')->length(5)->default(0);
            $table->integer('fiveHundreds')->length(5)->default(0);
            $table->integer('twoHundreds')->length(5)->default(0);
            $table->integer('oneHundreds')->length(5)->default(0);
            $table->integer('fifties')->length(5)->default(0);
            $table->integer('twenties')->length(5)->default(0);
            $table->integer('tens')->length(5)->default(0);
            $table->integer('fives')->length(5)->default(0);
            $table->integer('ones')->length(5)->default(0);
            $table->integer('fiftyCentavos')->length(5)->default(0);
            $table->integer('twentyFiveCentavos')->length(5)->default(0);
            $table->integer('tenCentavos')->length(5)->default(0);
            $table->integer('fiveCentavos')->length(5)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_on_hand_histories');
    }
};
