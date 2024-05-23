<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderService extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'jobOrderID',
        'serviceID',
        'amountPaid'
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener for updating an existing JobOrderService
        static::updating(function ($jobOrderService) {
            // Retrieve the JobOrder associated with this JobOrderService
            $jobOrder = JobOrder::findOrFail($jobOrderService->jobOrderID);

            // Find the corresponding finance record and update it
            $finance = Finance::where('jobOrderID', $jobOrder->id)->first();

            if ($finance) {
                $finance->update([
                    'amount' => $jobOrderService->amountPaid,
                    // Update other relevant fields as needed
                ]);
            }
        });
    }

    public function job_order()
    {
        return $this->belongsTo(JobOrder::class, 'jobOrderID');
    }
}
