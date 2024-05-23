<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Inventory;
use App\Models\InventoryHistory;
use App\Models\Supplier;
use App\Models\Finance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
   
class SaleController extends Controller
{
    /**
     * Display main page for Purchase
     * 
     * @return Response
     */
    public function main(){
        return Inertia::render('Sales/Main');
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return Response
     */
    public function index(){
        $sale = Sale::all();
        $salesItems = SalesItem::all();
        $items = Inventory::all();
        return Inertia::render('Sales/Index', ['sales' => $sale, 'salesItems' => $salesItems, 'inventories' => $items]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create()
    {
        $salesID = Session::get('salesID');

        if (!$salesID || !Sale::where('id', $salesID)->exists()) {
            $newSalesOrder = new Sale();
            $newSalesOrder->salesDate = Carbon::today();
            $newSalesOrder->save();
            $salesID = $newSalesOrder->id;

            Session::put('salesID', $salesID);
        }

        $item = Inventory::all();
        
        return Inertia::render('Sales/Create', ['sales' => Sale::find($salesID), 'inventories' => $item]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->salesID;
        Validator::make($request->all(), [
            'generalNotes' => ['nullable', 'string']
        ])->validate();
        try{
            \DB::beginTransaction();
            $totalSales = SalesItem::where('salesID', $id)->sum('amountPaid');
            $sale = Sale::find($id);
            $sale->totalSales = $totalSales;
            $sale->generalNotes = $request->generalNotes;
            $sale->save();
            
            $finance = Finance::updateOrCreate(
                ['salesID' => $id],
                [
                    'amount' => $totalSales,
                    'transactionType' => 'Sale',
                    'generalNotes' => $request->generalNotes,
                ]
            );

            $salesItems = SalesItem::where('salesID', $id)->get();
            foreach ($salesItems as $salesItem) {
                $itemID = $salesItem->itemID;
                $item = Inventory::find($itemID);
        
                if ($item) {
                    $item->decrement('itemQuantity', $salesItem->quantitySold);
                    $item->save();
                }

                // Create InventoryHistory record with new quantity as the 'quantity' column
                InventoryHistory::create([
                    'itemID' => $item['id'],
                    'itemSerialNumber' => $item->itemSerialNumber,
                    'itemName' => $item->itemName,
                    'itemCategory' => $item->itemCategory,
                    'itemModel' => $item->itemModel,
                    'itemLocation' => $item->itemLocation,
                    'itemQuantity' => $item->itemQuantity, // Set the new quantity here
                    'itemUnit' => $item->itemUnit,
                    'itemThreshold' => $item->itemThreshold,
                    'itemCostPrice' => $item->itemCostPrice,
                    'itemSRP' => $item->itemSRP,
                    'isNotifying' => $item->isNotifying,
                    'isDisabled' => $item->isDisabled,
                    'supplierID' => $item->supplierID,
                    'generalNotes' => "Sale: $salesItem->quantitySold items",
                    // Add other columns as needed
                ]);
            }
            \DB::commit();

            Session::forget('salesID');
            return redirect()->route('sales.index');
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving sales order: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the sales order.'], 500);
        }
    }
    /*
    public function edit(Sale $sales){
        $sales->load('items');
        $salesItems = $sales->items;
        $inventory = Inventory::all();
        $suppliers = Supplier::all();

        return Inertia::render('Sales/Edit',[
            'sales' => $sales,
            'salesItems' => $salesItems,
            'inventories' => $inventory,
            'suppliers' => $suppliers
        ]);
    }*/
}
