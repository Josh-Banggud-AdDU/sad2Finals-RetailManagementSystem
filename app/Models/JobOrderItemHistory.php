<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderItemHistory extends Model
{
    use HasFactory;

    protected $table = 'job_order_item_histories';

    protected $fillable = [
        'jobOrderID',
        'joHistoryID',
        'itemID',
        'quantityUsed',
        'amountPaid',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrderHistory::class, 'jobOrderID');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'itemID');
    }
}
