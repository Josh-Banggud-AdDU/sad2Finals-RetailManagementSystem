<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InventoryLogItem;
use App\Models\InventoryLog;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Finances;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
   
class InventoryLogItemController extends Controller
{
    // ...

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'inventory_logID' => ['required', 'exists:inventory_logs,id'],
            'inventoryLogItems' => ['required', 'array'],
        ])->validate();

        $inventory_logID = $request->input('inventory_logID');
        $inventoryLog = InventoryLog::find($inventory_logID);

        $inventoryLogItems = $request->input('inventoryLogItems');

        try {
            \DB::beginTransaction();

            foreach ($inventoryLogItems as $item) {
                $inventoryItem = Inventory::find($item['itemID']);
                $transactionType = $inventoryLog->transactionType;

                $originalQuantity = $inventoryItem->itemQuantity;
                if ($transactionType === 'Stock In' || $transactionType === 'Item Return') {
                    $inventoryItem->increment('itemQuantity', $item['quantity']);
                } elseif ($transactionType === 'Stock Out') {
                    $inventoryItem->decrement('itemQuantity', $item['quantity']);
                }
                $updatedQuantity = $inventoryItem->itemQuantity;

                // Save the inventory log item with the correct quantity
                $inventoryLogItem = new InventoryLogItem([
                    'inventory_logID' => $inventory_logID,
                    'itemID' => $item['itemID'],
                    'quantity' => $item['quantity'],
                    'generalNotes' => $item['generalNotes'],
                ]);
                
                $inventoryLogItem->save();
                
                $quantityChange = $inventoryLogItem->quantity;

                // Store changes in the InventoryHistory table for quantity updates
                InventoryHistory::create([
                    'itemID' => $item['itemID'],
                    'itemSerialNumber' => $inventoryItem->itemSerialNumber,
                    'itemName' => $inventoryItem->itemName,
                    'itemCategory' => $inventoryItem->itemCategory,
                    'itemModel' => $inventoryItem->itemModel,
                    'itemLocation' => $inventoryItem->itemLocation,
                    'itemQuantity' => $updatedQuantity,
                    'itemUnit' => $inventoryItem->itemUnit,
                    'itemThreshold' => $inventoryItem->itemThreshold,
                    'itemCostPrice' => $inventoryItem->itemCostPrice,
                    'itemSRP' => $inventoryItem->itemSRP,
                    'itemMarkup' => $inventoryItem->itemMarkup,
                    'isNotifying' => $inventoryItem->isNotifying,
                    'isDisabled' => $inventoryItem->isDisabled,
                    'supplierID' => $inventoryItem->supplierID,
                    'generalNotes' => "$transactionType: $quantityChange",
                ]);
            }

            \DB::commit();

            return response()->json(['message' => 'Inventory log items saved successfully']);
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving inventory log items: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the inventory log items.'], 500);
        }
    }
}