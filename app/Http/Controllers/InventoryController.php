<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\InventoryLogItem;
use App\Models\InventoryHistory;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\SalesItem;
use Illuminate\Support\Facades\Validator;
   
class InventoryController extends Controller
{
    /**
     * Display main page for Purchase
     * 
     * @return Response
     */
    public function main(){
        return Inertia::render('Inventory/Main');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {
        $item = Inventory::all();
        return Inertia::render('Inventory/Index', ['inventories' => $item]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $inventories = Inventory::all();
        return Inertia::render('Inventory/Create', ['suppliers' => $suppliers, 'inventories' => $inventories]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'itemSerialNumber' => ['required', 'string'],
            'itemName' => ['required', 'string'],
            'itemCategory' => ['required', 'string'],
            'itemModel' => ['nullable', 'string'],
            'itemUnit' => ['required', 'string'],
            'itemThreshold' => ['required', 'numeric'],
            'itemCostPrice' => ['required', 'numeric'],
            'itemSRP' => ['required', 'numeric'],
            'supplierID' => ['required', 'exists:suppliers,id']
        ])->validate();

        $itemCostPrice = $request->input('itemCostPrice');
        $itemSRP = $request->input('itemSRP');
        $itemMarkup = round((($itemSRP - $itemCostPrice) / $itemCostPrice) * 100,2);
        
        $inventory = Inventory::create(array_merge(
            $request->all(),
            ['itemMarkup' => $itemMarkup]
        ));

        // Create a record in InventoryHistory for the creation
        InventoryHistory::create([
            'itemID' => $inventory->id,
            'itemSerialNumber' => $inventory->itemSerialNumber,
            'itemName' => $inventory->itemName,
            'itemCategory' => $inventory->itemCategory,
            'itemModel' => $inventory->itemModel,
            'itemUnit' => $inventory->itemUnit,
            'itemThreshold' => $inventory->itemThreshold,
            'itemCostPrice' => $inventory->itemCostPrice,
            'itemSRP' => $inventory->itemSRP,
            'itemMarkup' => $inventory->itemMarkup,
            'supplierID' => $inventory->supplierID,
        ]);

        return redirect()->route('inventories.index');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::all();
        $inventory_history = InventoryHistory::all();
        $inventories = Inventory::all();

        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory,
            'suppliers' => $suppliers,
            'inventory_history' => $inventory_history,
            'inventories' => $inventories
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        Validator::make($request->all(), [
            'itemSerialNumber' => ['required', 'string'],
            'itemName' => ['required', 'string'],
            'itemCategory' => ['required', 'string'],
            'itemModel' => ['nullable', 'string'],
            'itemLocation' => ['required', 'string'],
            'itemQuantity' => ['required', 'numeric'],
            'itemUnit' => ['required', 'string'],
            'itemThreshold' => ['required', 'numeric'],
            'itemCostPrice' => ['required', 'numeric'],
            'itemSRP' => ['required', 'numeric'],
            'isNotifying' => ['required', 'boolean'],
            'isDisabled' => ['required', 'boolean'],
            'supplierID' => ['required', 'exists:suppliers,id']
        ])->validate();
    
        $inventory = Inventory::find($id);

        $itemCostPrice = $request->input('itemCostPrice');
        $itemSRP = $request->input('itemSRP');
        $itemMarkup = round((($itemSRP - $itemCostPrice) / $itemCostPrice) * 100,2);

        //$updatedInventory = $request->all();
        //$updatedInventory['itemMarkup'] = $itemMarkup;

        $inventory->update(array_merge(
            $request->all(),
            ['itemMarkup' => $itemMarkup]
        ));

        // Store changes in history table
        InventoryHistory::create([
            'itemID' => $id,
            'itemSerialNumber' => $inventory->itemSerialNumber,
            'itemName' => $inventory->itemName,
            'itemCategory' => $inventory->itemCategory,
            'itemModel' => $inventory->itemModel,
            'itemLocation' => $inventory->itemLocation,
            'itemQuantity' => $inventory->itemQuantity,
            'itemUnit' => $inventory->itemUnit,
            'itemThreshold' => $inventory->itemThreshold,
            'itemCostPrice' => $inventory->itemCostPrice,
            'itemSRP' => $inventory->itemSRP,
            'itemMarkup' => $inventory->itemMarkup,
            'isNotifying' => $inventory->isNotifying,
            'isDisabled' => $inventory->isDisabled,
            'supplierID' => $inventory->supplierID,
            'generalNotes' => $request->input('generalNotes'), // Assuming 'notes' is the field for general notes
        ]);

        return redirect()->route('inventories.index');
    
    }

    public function manage_supply(){
        $inventory_logs = InventoryLog::all();
        $item = Inventory::all();
        $inventory_log_items = InventoryLogItem::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $salesItems = SalesItem::all();
        return Inertia::render('Inventory/Supply_Management', [
            'inventory_logs' => $inventory_logs,
            'inventories' => $item,
            'inventory_log_items' => $inventory_log_items,
            'suppliers' => $suppliers,
            'employees' => $employees,
            'salesItems' => $salesItems
        ]);
    }

    public function search(Request $request){
        $searchItem = $request->input('searchItem');
        $results= Inventory::where('itemName', 'like', "%$searchItem%")->get();

        return response()->json($results);
    }

    /*public function searchItemOrSupplier(Request $request){
        $query = $request->input('query');

        $inventoryResults = Inventory::where('itemName', 'like', "%$query%")->get();
        $supplierResults = Supplier::where('supplierName', 'like', "%$query%")->get();

        return response()->json([
            'inventory' => $inventoryResults,
            'suppliers' => $supplierResults
        ]);
    }*/

    public function showUnits(){
        $results = Inventory::distinct()->pluck('itemUnit')->toArray();
        return response()->json($results);
    }

    public function showCategories(){
        $results = Inventory::distinct()->pluck('itemCategory')->toArray();
        return response()->json($results);
    }
}