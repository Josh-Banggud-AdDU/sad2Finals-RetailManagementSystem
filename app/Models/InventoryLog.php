<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transactionType',
        'transactionDate',
        'referenceNum',
        'generalNotes',
        'employeeID',
        'salesID'
        //supplier id is in php but i dont see how its relevant to this table
    ];

    public function inventory_log_items(){
        return $this->hasMany(InventoryLogItem::class, 'inventory_logID', 'id');
    }
}
