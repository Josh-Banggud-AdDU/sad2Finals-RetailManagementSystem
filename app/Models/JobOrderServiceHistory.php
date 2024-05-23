<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderServiceHistory extends Model
{
    use HasFactory;

    protected $table = 'job_order_service_histories';

    protected $fillable = [
        'jobOrderID',
        'joHistoryID',
        'serviceID',
        'amountPaid',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrderHistory::class, 'jobOrderID');
    }
}
