<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\InventoryLog;
use App\Models\InventoryLogItem;
use App\Models\Inventory;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Sale;
use App\Models\SalesItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
   
class InventoryLogController extends Controller
{
    public function edit(InventoryLog $inventoryLog)
    {
        //dd($inventoryLog);
        $inventoryLog->load('inventory_log_items');
        $inventoryLogItems = $inventoryLog->inventory_log_items;
        $inventory = Inventory::all();
        $suppliers = Supplier::all();
        return Inertia::render('SupplyManagement/Edit', [
            'inventory_logs' => $inventoryLog,
            'inventory_log_items' => $inventoryLogItems,
            'inventories' => $inventory,
            'suppliers' => $suppliers
        ]);
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function stock_in(Request $request)
    {
        $inventory_logID = Session::get('inventory_logID');
        if (!$inventory_logID || !InventoryLog::where('id', $inventory_logID)->exists()) {
            $newInventoryLog = new InventoryLog();
            $newInventoryLog->transactionDate = Carbon::today();
            $newInventoryLog->transactionType = "Stock In";
            $newInventoryLog->save();
            $inventory_logID = $newInventoryLog->id;
            
            Session::put('inventory_logID', $inventory_logID);
        }else {
            $existingInventoryLog = InventoryLog::find($inventory_logID);
            $existingInventoryLog->transactionType = "Stock In";
            $existingInventoryLog->save();
        }

        $item = Inventory::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $all_inventory_logs = InventoryLog::all();

        return Inertia::render('SupplyManagement/Stock_In', [
            'inventory_logs' => InventoryLog::find($inventory_logID),
            'inventories' => $item,
            'suppliers' => $suppliers,
            'employees' => $employees,
            'all_inventory_logs' => $all_inventory_logs
        ]);
    }

    public function stock_out()
    {
        $inventory_logID = Session::get('inventory_logID');

        if (!$inventory_logID || !InventoryLog::where('id', $inventory_logID)->exists()) {
            $newInventoryLog = new InventoryLog();
            $newInventoryLog->transactionDate = Carbon::today();
            $newInventoryLog->transactionType = "Stock Out";
            $newInventoryLog->save();
            $inventory_logID = $newInventoryLog->id;

            Session::put('inventory_logID', $inventory_logID);
        }
        else {
            $existingInventoryLog = InventoryLog::find($inventory_logID);
            $existingInventoryLog->transactionType = "Stock Out";
            $existingInventoryLog->save();
        }

        $item = Inventory::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $all_inventory_logs = InventoryLog::all();

        return Inertia::render('SupplyManagement/Stock_Out', [
            'inventory_logs' => InventoryLog::find($inventory_logID),
            'inventories' => $item,
            'suppliers' => $suppliers,
            'employees' => $employees,
            'all_inventory_logs' => $all_inventory_logs
        ]);
    }

    public function item_return()
    {
        $inventory_logID = Session::get('inventory_logID');

        if (!$inventory_logID || !InventoryLog::where('id', $inventory_logID)->exists()) {
            $newInventoryLog = new InventoryLog();
            $newInventoryLog->transactionDate = Carbon::today();
            $newInventoryLog->transactionType = "Item Return";
            $newInventoryLog->save();
            $inventory_logID = $newInventoryLog->id;

            Session::put('inventory_logID', $inventory_logID);
        }
        else {
            $existingInventoryLog = InventoryLog::find($inventory_logID);
            $existingInventoryLog->transactionType = "Item Return";
            $existingInventoryLog->save();
        }

        $item = Inventory::all();
        $suppliers = Supplier::all();
        $employees = Employee::all();
        $all_inventory_logs = InventoryLog::all();
        $sales = Sale::all();
        $salesItems = SalesItem::all();
        $inventory_logs_items = InventoryLogItem::all();

        return Inertia::render('SupplyManagement/Item_Return', [
            'inventory_logs' => InventoryLog::find($inventory_logID),
            'inventories' => $item,
            'suppliers' => $suppliers,
            'employees' => $employees,
            'all_inventory_logs' => $all_inventory_logs,
            'sales' => $sales,
            'salesItems' => $salesItems,
            'inventory_logs_items' => $inventory_logs_items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->inventory_logID;
        Validator::make($request->all(), [
            'employeeID' => ['required', 'exists:employees,id'],
            'supplierID' => ['nullable', 'exists:suppliers,id'],
            'referenceNum' => ['nullable', 'string'],
            'generalNotes' => ['nullable', 'string'],
            'salesID' => ['nullable', 'exists:sales,id']
        ])->validate();
        try {
            \DB::beginTransaction();
            $inventoryLog = InventoryLog::find($id);
            $inventoryLog->employeeID = $request->employeeID;
            $inventoryLog->supplierID = $request->supplierID;
            $inventoryLog->referenceNum = $request->referenceNum;
            $inventoryLog->generalNotes = $request->generalNotes;
            $inventoryLog->salesID = $request->salesID;
            try {
                $inventoryLog->save();
            } catch (\Exception $e) {
                \Log::error("Error saving inventory log: " . $e->getMessage());
            }
            \DB::commit();

            Session::forget('inventory_logID');
            return redirect()->route('inventories.manage_supply');
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving inventory log: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the inventory log.'], 500);

        }
    }
}