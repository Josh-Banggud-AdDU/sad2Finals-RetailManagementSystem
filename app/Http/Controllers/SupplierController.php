<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
   
class SupplierController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request): void
    {
        Validator::make($request->all(), [
            'supplierName' => ['required', 'string'],
        ])->validate();

        //dd($request->all());
        Supplier::create($request->all());
    }
    
    public function search(Request $request){
        $searchName = $request->input('searchName');

        $results = Supplier::where('supplierName', 'like', "%$searchName%")->get();
        return response()->json($results);
    }
}