<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
   
class ServiceController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {
        $services = Service::all();
        return Inertia::render('Service/Index', ['services' => $services]);
    }
  


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return Inertia::render('Inventory/Create', ['suppliers' => $suppliers]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'serviceName' => ['required', 'string']
        ])->validate();

        $service = Service::create($request->all());

        // Create a record in InventoryHistory for the creation
    }

    /*
    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::all();
        return Inertia::render('Inventory/Edit', [
            'inventory' => $inventory,
            'suppliers' => $suppliers
        ]);
    }
    
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
    
        // Retrieve the existing inventory item
        $inventory = Inventory::find($id);

        // Update the inventory item
        $inventory->update($request->all());

        return redirect()->route('inventories.index');
    
    }*/

    public function search(Request $request){
        $searchService = $request->input('searchService');
        $results= Service::where('serviceName', 'like', "%$searchService%")->get();

        return response()->json($results);
    }
}