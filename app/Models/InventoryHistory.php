<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemID',
        'itemSerialNumber',
        'itemName',
        'itemCategory',
        'itemModel',
        'itemLocation',
        'itemQuantity',
        'itemUnit',
        'itemThreshold',
        'itemCostPrice',
        'itemSRP',
        'itemMarkup',
        'isNotifying',
        'isDisabled',
        'supplierID',
        'generalNotes',
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }
}
