<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\JobOrderItemHistory;
use App\Models\Inventory;
use Illuminate\Support\Facades\Validator;
   
class JobOrderItemController extends Controller
{
    /**
     * Show the form for creating a new resource.
     * 
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'jobOrderID' => ['required', 'exists:job_orders,id'],
            'jobOrderItems' => ['nullable', 'array']
        ])->validate();
        
        $jobOrderID = $request->input('jobOrderID');

        $jobOrderItems = $request->input('jobOrderItems');
        try{
            if(!empty($jobOrderItems)){
                foreach($jobOrderItems as $item){
                    $jobOrderItem = new JobOrderItem([
                        'jobOrderID' => $jobOrderID,
                        'itemID' => $item['itemID'],
                        'quantityUsed' => $item['quantityUsed'],
                        'amountPaid' => $item['amountPaid'],
                    ]);
                    
                    $jobOrderItem->save();

                    Inventory::where('id', $item['itemID'])->decrement('itemQuantity', $item['quantityUsed']);
                }
            }

            /*
            foreach ($jobOrderItems as $item) {
                JobOrderItemHistory::create($item);
            }
            */

            \DB::commit();
        }catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving job order items: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the job order items.'], 500);
        }
    }
    public function update(Request $request){
        try{
            \DB::beginTransaction();

            Validator::make($request->all(), [
                'jobOrderID' => ['required', 'exists:job_orders,id'],
                'jobOrderItems' => ['nullable', 'array']
            ])->validate();

            $jobOrderID = $request->input('jobOrderID');
            $jobOrderItems = $request->input('jobOrderItems');

            //EXTRACT current data
            $existingItemsData = JobOrderItem::where('jobOrderID', $jobOrderID)->get();

            //ROLLBACK items to previous quantity
            foreach($existingItemsData as $existingItem){
                $rollbackItem = Inventory::where('id', $existingItem->itemID)->first();
                $rollbackItem->increment('itemQuantity',$existingItem->quantityUsed);
            }

            //REMOVE items in database that are not in the input
            $existingItems = JobOrderItem::where('jobOrderID', $jobOrderID)->pluck('itemID');
            $updatedItems = collect($jobOrderItems)->pluck('itemID');
            $omittedItems = $existingItems->diff($updatedItems);
            JobOrderItem::where('jobOrderID', $jobOrderID)->wherein('itemID', $omittedItems)->delete();

            //UPDATE & ADD items to the database from the input
            if(!empty($jobOrderItems)){
                foreach($jobOrderItems as $item){
                    $jobOrderItem = JobOrderItem::updateOrCreate([
                        'jobOrderID' => $jobOrderID,
                        'itemID' => $item['itemID'],
                    ],[
                        'quantityUsed' => $item['quantityUsed'],
                        'amountPaid' => $item['amountPaid'],
                    ]);

                    Inventory::where('id', $item['itemID'])->decrement('itemQuantity', $item['quantityUsed']);
                }
            }
            \DB::commit();
        } catch(\Exception $e){
            \DB::rollBack();
            \Log::error("Error updating job order items: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the job order items.'], 500);
        }
    }
}