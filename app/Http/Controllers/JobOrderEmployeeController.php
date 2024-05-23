<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobOrder;
use App\Models\JobOrderEmployee;
use App\Models\JobOrderEmployeeHistory;
use Illuminate\Support\Facades\Validator;
   
class JobOrderEmployeeController extends Controller
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
            'jobOrderEmployees' => ['required', 'array']
        ])->validate();
        
        $jobOrderID = $request->input('jobOrderID');

        $jobOrderEmployees = $request->input('jobOrderEmployees');
        try{
            foreach($jobOrderEmployees as $employee){
                $jobOrderEmployee = new JobOrderEmployee([
                    'jobOrderID' => $jobOrderID,
                    'employeeID' => $employee['employeeID']
                ]);

                $jobOrderEmployee->save();
            }

            /*
            foreach ($jobOrderEmployees as $employee) {
                $employee['jobOrderID'] = $jobOrderID; // Set the jobOrderID before creating the history record
                JobOrderEmployeeHistory::create($employee);
            }*/
            
            \DB::commit();
        }catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving job order employees: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the job order employees.'], 500);
        }
    }
    public function update(Request $request){
        try{
            \DB::beginTransaction();

            Validator::make($request->all(), [
                'jobOrderID' => ['required', 'exists:job_orders,id'],
                'jobOrderEmployees' => ['required', 'array']
            ])->validate();

            $jobOrderID = $request->input('jobOrderID');
            $jobOrderEmployees = $request->input('jobOrderEmployees');

            //REMOVE employees in database that are not in the input
            $existingEmployees = JobOrderEmployee::where('jobOrderID', $jobOrderID)->pluck('employeeID');
            $updatedEmployees = collect($jobOrderEmployees)->pluck('employeeID');
            $omittedEmployees = $existingEmployees->diff($updatedEmployees);
            JobOrderEmployee::where('jobOrderID', $jobOrderID)->wherein('employeeID', $omittedEmployees)->delete();

            //UPDATE & ADD employees to the database from the input
            foreach($jobOrderEmployees as $employee){
                $jobOrderEmployee = JobOrderEmployee::updateOrCreate([
                    'jobOrderID' => $jobOrderID,
                    'employeeID' => $employee['employeeID'],
                ]);
            }

            /*foreach ($jobOrderEmployees as $employee) {
                $employee['jobOrderID'] = $jobOrderID; // Set the jobOrderID before creating the history record
                JobOrderEmployeeHistory::create($employee);
            }*/

            \DB::commit();
        } catch(\Exception $e){
            \DB::rollBack();
            \Log::error("Error updating job order services: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the job order services.'], 500);
        }
    }
}