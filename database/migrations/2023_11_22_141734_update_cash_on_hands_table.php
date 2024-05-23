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
        Schema::table('cash_on_hands', function (Blueprint $table) {
            $table->dropColumn('ones');
            $table->dropColumn('tens');
            $table->integer('thousands')->length(5);
            $table->integer('fiveHundreds')->length(5);
            $table->integer('twoHundreds')->length(5);
            $table->integer('oneHundreds')->length(5);
            $table->integer('fifties')->length(5);
            $table->integer('twenties')->length(5);
            $table->integer('fives')->length(5);
            $table->integer('fiftyCentavos')->length(5);
            $table->integer('twentyFiveCentavos')->length(5);
            $table->integer('tenCentavos')->length(5);
            $table->integer('fiveCentavos')->length(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
