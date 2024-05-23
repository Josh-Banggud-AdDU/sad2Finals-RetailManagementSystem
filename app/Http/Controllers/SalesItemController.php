<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SalesItem;
use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
   
class SalesItemController extends Controller
{
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'salesID' => ['required', 'exists:sales,id'],
            'salesItems' => ['required', 'array'],
        ])->validate();
        
        $salesID = $request->input('salesID');
        //$salesOrder = Sale::find($salesID);

        $salesItems = $request->input('salesItems');
        try{
            foreach($salesItems as $item){
                $salesItem = new SalesItem([
                    'salesID' => $salesID,
                    'itemID' => $item['itemID'],
                    'quantitySold' => $item['quantitySold'],
                    'amountPaid' => $item['amountPaid'],
                ]);

                $salesItem->save();
                //$salesOrder->items()->save($salesItem);

                //SALES HISTORY HERE
            }

            \DB::commit();

            //return response()->json(['message' => 'Sales order items saved successfully']);
        }catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving sales order items: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the sales order items.'], 500);
        }
    }

    /*
    public function update(Request $request)
    {
        try {
            \DB::enableQueryLog();
            \DB::beginTransaction();

            Validator::make($request->all(), [
                'salesID' => ['required', 'exists:sales,id'],
                'salesItems' => ['required', 'array'],
            ])->validate();
            
            $salesID = $request->input('salesID');
            //$salesOrder = Sale::find($salesID);

            $salesItems = $request->input('salesItems');

            $existingItems = SalesItem::where('salesID', $salesID)->pluck('itemID');
            $inputItemIDs = collect($salesItems)->pluck('itemID');
            dd($existingItems, $inputItemIDs);
            $itemsToRemove = $existingItems->diff($inputItemIDs);
            SalesItem::where('salesID', $salesID)->whereIn('itemID', $itemsToRemove)->delete();

            foreach ($salesItems as $item) {
                $salesItem = SalesItem::updateOrCreate([
                    'salesID' => $salesID,
                    'itemID' => $item['itemID'],
                ], [
                    'quantitySold' => $item['quantitySold'],
                    'amountPaid' => $item['amountPaid'],
                ]);

                // You may want to add sales history updates here

                // Save the updated sales item
                $salesItem->save();
            }

            \DB::commit();
            \Log::info(\DB::getQueryLog());

            return response()->json(['message' => 'Sales order items updated successfully']);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error updating sales order items: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the sales order items.'], 500);
        }
    }*/

}