<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderEmployeeHistory extends Model
{
    use HasFactory;

    protected $table = 'job_order_employee_histories';

    protected $fillable = [
        'jobOrderID',
        'joHistoryID',
        'employeeID',
    ];

    public function jobOrder()
    {
        return $this->belongsTo(JobOrderHistory::class, 'jobOrderID');
    }
}
