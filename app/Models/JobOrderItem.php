<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobOrderID',
        'itemID',
        'quantityUsed',
        'amountPaid'
    ];

    protected static function boot()
    {
        parent::boot();

        // Event listener for updating an existing JobOrderItem
        static::updating(function ($jobOrderItem) {
            // Retrieve the JobOrder associated with this JobOrderItem
            $jobOrder = JobOrder::findOrFail($jobOrderItem->jobOrderID);

            // Find the corresponding finance record and update it
            $finance = Finance::where('jobOrderID', $jobOrder->id)->first();

            if ($finance) {
                $finance->update([
                    'amount' => $jobOrderItem->amountPaid,
                    // Update other relevant fields as needed
                ]);
            }
        });
    }

    public function job_order()
    {
        return $this->belongsTo(JobOrder::class, 'jobOrderID');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'itemID');
    }
}
