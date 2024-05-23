<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'salesID',
        'jobOrderID',
        'cashflowID',
        'amount',
        'generalNotes',
        'transactionType'
    ];

    public function sales()
    {
        return $this->belongsTo(Sale::class, 'salesID');
    }

    public function jobOrder()
    {
        return $this->belongsTo(JobOrder::class, 'jobOrderID');
    }

    public function cashflow()
    {
        return $this->belongsTo(Cashflow::class, 'cashflowID');
    }
}
