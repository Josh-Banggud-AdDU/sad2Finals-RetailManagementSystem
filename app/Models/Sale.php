<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'totalSales',
        'salesDate',
        'generalNotes'
    ];

    public function items()
    {
        return $this->hasMany(SalesItem::class, 'salesID');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'id');
    }
}
