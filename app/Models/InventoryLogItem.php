<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLogItem extends Model
{
    use HasFactory;
    protected $table = 'inventory_logs_items';
    protected $fillable = [
        'inventory_logID',
        'itemID',
        'quantity',
        'generalNotes'
    ];

    public function inventory_log(){
        return $this->belongsTo(InventoryLog::class, 'inventory_logID');
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class, 'itemID');
    }
}
