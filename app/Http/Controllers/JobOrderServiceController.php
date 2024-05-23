<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobOrder;
use App\Models\JobOrderServiceHistory;
use App\Models\JobOrderService;
use Illuminate\Support\Facades\Validator;
   
class JobOrderServiceController extends Controller
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
            'jobOrderServices' => ['required', 'array']
        ])->validate();
        
        $jobOrderID = $request->input('jobOrderID');

        $jobOrderServices = $request->input('jobOrderServices');
        try{
            foreach($jobOrderServices as $service){
                $jobOrderService = new JobOrderService([
                    'jobOrderID' => $jobOrderID,
                    'serviceID' => $service['serviceID'],
                    'amountPaid' => $service['amountPaid'],
                ]);

                $jobOrderService->save();
            }

            /*
            foreach ($jobOrderServices as $service) {
                JobOrderServiceHistory::create($service);
            }
            */

            \DB::commit();
        }catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error saving job order services: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the job order services.'], 500);
        }
    }
    public function update(Request $request){
        try{
            \DB::beginTransaction();

            Validator::make($request->all(), [
                'jobOrderID' => ['required', 'exists:job_orders,id'],
                'jobOrderServices' => ['required', 'array']
            ])->validate();

            $jobOrderID = $request->input('jobOrderID');
            $jobOrderServices = $request->input('jobOrderServices');

            //REMOVE services in database that are not in the input
            $existingServices = JobOrderService::where('jobOrderID', $jobOrderID)->pluck('serviceID');
            $updatedServices = collect($jobOrderServices)->pluck('serviceID');
            $omittedServices = $existingServices->diff($updatedServices);
            JobOrderService::where('jobOrderID', $jobOrderID)->wherein('serviceID', $omittedServices)->delete();

            //UPDATE & ADD services to the database from the input
            foreach($jobOrderServices as $service){
                $jobOrderService = JobOrderService::updateOrCreate([
                    'jobOrderID' => $jobOrderID,
                    'serviceID' => $service['serviceID'],
                ],[
                    'amountPaid' => $service['amountPaid'],
                ]);
            }

            \DB::commit();
        } catch(\Exception $e){
            \DB::rollBack();
            \Log::error("Error updating job order services: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the job order services.'], 500);
        }
    }
}