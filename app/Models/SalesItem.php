<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'salesID',
        'itemID',
        'quantitySold',
        'amountPaid'
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener for updating an existing SalesItem
        static::updating(function ($salesItem) {
            // Retrieve the Sale associated with this SalesItem
            $sale = Sale::findOrFail($salesItem->salesID);

            // Find the corresponding finance record and update it
            $finance = Finance::where('salesID', $sale->id)->first();

            if ($finance) {
                $finance->update([
                    'amount' => $salesItem->amountPaid,
                    // Update other relevant fields as needed
                ]);
            }
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class, 'salesID');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'itemID');
    }
}
