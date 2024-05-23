<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOnHandHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_on_hand_id',
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
    ];

    // Relationship with the CashOnHand model
    public function cashOnHand()
    {
        return $this->belongsTo(CashOnHand::class, 'cash_on_hand_id');
    }
}
