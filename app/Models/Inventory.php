<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
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
    ];

    public function history()
    {
        return $this->hasMany(InventoryHistory::class);
    }
}
