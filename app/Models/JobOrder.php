<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobOrderStatus',
        'paymentStatus',
        'total',
        'amountDue',
        'profit',
        'vehicleDetails',
        'jobOrderDate',
        'generalNotes'
    ];

    // Relationship with JobOrderItem model
    public function items()
    {
        return $this->hasMany(JobOrderItem::class, 'jobOrderID');
    }

    public function services()
    {
        return $this->hasMany(JobOrderService::class, 'jobOrderID');
    }

    public function employees()
    {
        return $this->hasMany(JobOrderEmployee::class, 'jobOrderID');
    }

    // Relationship with Inventory model
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'itemID');
    }
}
