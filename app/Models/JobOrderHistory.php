<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderHistory extends Model
{
    use HasFactory;

    protected $table = 'job_order_histories';

    protected $fillable = [
        'jobOrderID', // Add this line
        'jobOrderStatus',
        'paymentStatus',
        'total',
        'amountDue',
        'profit',
        'jobOrderDate',
        'vehicleDetails',
        'generalNotes',
    ];


    public function items()
    {
        return $this->hasMany(JobOrderItemHistory::class, 'jobOrderID');
    }

    public function services()
    {
        return $this->hasMany(JobOrderServiceHistory::class, 'jobOrderID');
    }

    public function employees()
    {
        return $this->hasMany(JobOrderEmployeeHistory::class, 'jobOrderID');
    }
}
