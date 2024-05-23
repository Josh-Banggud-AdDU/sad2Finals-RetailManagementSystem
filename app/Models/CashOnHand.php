<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOnHand extends Model
{
    use HasFactory;

    protected $fillable = [
        'thousands',
        'fiveHundreds',
        'twoHundreds',
        'oneHundreds',
        'fifties',
        'twenties',
        'tens',
        'fives',
        'ones',
        'twentyFiveCentavos',
        'tenCentavos',
        'fiveCentavos',
        'oneCentavos',
        'type',
    ];

    public function getTotalAmount()
    {
        // Calculate the total amount based on the bill denominations
        $totalAmount = (
            $this->thousands * 1000.00 +
            $this->fiveHundreds * 500.00 +
            $this->twoHundreds * 200.00 +
            $this->oneHundreds * 100.00 +
            $this->fifties * 50.00 +
            $this->twenties * 20.00 +
            $this->tens * 10.00 +
            $this->fives * 5.00 +
            $this->ones * 1.00 +
            $this->twentyFiveCentavos * 0.25 +
            $this->tenCentavos * 0.10 +
            $this->fiveCentavos * 0.05 +
            $this->oneCentavos * 0.01
        );

        return $totalAmount;
    }
}
