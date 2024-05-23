<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
   
class EmployeeController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index()
    {
        $employee = Employee::all();
        return Inertia::render('Employee/Index', ['employees' => $employee]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create()
    {
        return Inertia::render('Employee/Create');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'firstName' => ['required','string'],
            'lastName' => ['required','string'],
            'number' => ['nullable','string'],
            'address' => ['required','string'],
            'position' => ['required','string'],
        ])->validate();

        $employee = Employee::create($request->all());

        if($request->position == 'Clerk'){
            $roleID = 2;
        }else if($request->position == 'Mechanic'){
            $roleID = 3;
        }else{
            $roleID = 1;
        }
        $employee->roleID = $roleID;
        $employee->save();

        $name = $request->input('firstName').' '.$request->input('lastName');
        //\Log::info($name);
        Auth::logout();

        $request->session()->invalidate();
 
        $request->session()->regenerateToken();

        $request->session()->flash('name', $name);
    
        return redirect('/register?name=' . urlencode($name));
        //return Inertia::render('/register', ['name' => $name]);
        
        //return Inertia::location(route('logout') . '?name=' . urlencode($name));
        //return redirect()->route('logout')->with('name', $name);
        //return redirect()->route('employees.index');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('Employee/Edit', [
            'employees' => $employee
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
            'firstName' => ['required','string'],
            'lastName' => ['required','string'],
            'number' => ['nullable','string'],
            'email' => ['required','string'],
            'address' => ['required','string'],
            'position' => ['required','string'],
        ])->validate();
    
        Employee::find($id)->update($request->all());
        return redirect()->route('employees.index');
    }

    public function search(Request $request){
        $searchEmployee = $request->input('searchEmployee');
        $results = Employee::where(function ($query) use ($searchEmployee) {
            $query->where('firstName', 'like', "%$searchEmployee%")
                  ->orWhere('lastName', 'like', "%$searchEmployee%")
                  ->orWhere(DB::raw("CONCAT(firstName, ' ', lastName)"), 'like', "%$searchEmployee%");
        })->get();

        return response()->json($results);
    }
}