<?php
   
namespace App\Http\Controllers;
    
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JobOrder;
use App\Models\JobOrderHistory;
use App\Models\JobOrderItemHistory;
use App\Models\JobOrderEmployeeHistory;
use App\Models\JobOrderServiceHistory;
use App\Models\JobOrderItem;
use App\Models\JobOrderService;
use App\Models\JobOrderEmployee;
use App\Models\Inventory;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Finance;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

   
class JobOrderController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    public function main(){
        return Inertia::render('JobOrder/Main');
    }

    public function index()
    {
        $jobOrder = JobOrder::all();
        $jobOrderItems = JobOrderItem::all();
        $jobOrderServices = JobOrderService::all();
        $jobOrderEmployees = JobOrderEmployee::all();
        $items = Inventory::all();
        $services = Service::all();
        $employees = Employee::all();
        return Inertia::render('JobOrder/Index', [
            'jobOrders' => $jobOrder,
            'jobOrderItems' => $jobOrderItems,
            'jobOrderServices' => $jobOrderServices,
            'jobOrderEmployees' => $jobOrderEmployees,
            'inventories' => $items,
            'services' => $services,
            'employees' => $employees
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create()
    {
        $jobOrderID = Session::get('jobOrderID');

        // If no job order ID is found, generate a new one
        if (!$jobOrderID || !JobOrder::where('id', $jobOrderID)->exists()) {
            $newJobOrder = new JobOrder();
            $newJobOrder->jobOrderDate = Carbon::today();
            $newJobOrder->save();
            $jobOrderID = $newJobOrder->id;

            // Store the new job order ID in the session
            Session::put('jobOrderID', $jobOrderID);
        }

        $item = Inventory::all();
        $services = Service::all();
        $employees = Employee::all();
        return Inertia::render('JobOrder/Create', ['jobOrders' => JobOrder::find($jobOrderID), 'inventories' => $item, 'services' => $services, 'employees' => $employees]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $id = $request->jobOrderID;
        Validator::make($request->all(), [
            'vehicleDetails' => ['required', 'string'],
            'generalNotes' => ['nullable', 'string'],
            'total' => ['required', 'numeric']
        ])->validate();
        try{
            \DB::beginTransaction();
            $jobOrder = JobOrder::find($id);
            $jobOrderItems = JobOrderItem::where('jobOrderID', $jobOrder->id)->get();
            $jobOrderServices = JobOrderService::where('jobOrderID', $jobOrder->id)->get();
            $jobOrderEmployees = JobOrderEmployee::where('jobOrderID', $jobOrder->id)->get();
            $jobOrder->vehicleDetails = $request->vehicleDetails;
            $jobOrder->generalNotes = $request->generalNotes;
            $jobOrder->total = $request->total;
            $jobOrder->amountDue = $request->total;
            $jobOrder->save();

            $historyData = $jobOrder->toArray();
            $historyData['jobOrderID'] = $jobOrder->id;
            $jobOrderHistory = JobOrderHistory::create($historyData);

            foreach ($jobOrderItems as $item) {
                $itemHistoryData = $item->toArray();
                $itemHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $itemHistoryData['jobOrderID'] = $jobOrder->id;
                $jobOrderItemHistory = JobOrderItemHistory::create($itemHistoryData);
            }
    
            foreach ($jobOrderServices as $service) {
                $serviceHistoryData = $service->toArray();
                $serviceHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $serviceHistoryData['jobOrderID'] = $jobOrder->id;
                $jobOrderServiceHistory = JobOrderServiceHistory::create($serviceHistoryData);
            }
    
            foreach ($jobOrderEmployees as $employee) {
                $employeeHistoryData = $employee->toArray();
                $employeeHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $employeeHistoryData['jobOrderID'] = $jobOrder->id;
                $jobOrderEmployeeHistory = JobOrderEmployeeHistory::create($employeeHistoryData);
            }


            \DB::commit();

            Session::forget('jobOrderID');
            return redirect()->route('job_orders.index');
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving job order: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the job order.'], 500);
        }
    }

    public function edit(JobOrder $jobOrder){
        $jobOrder->load('items');
        $jobOrder->load('services');
        $jobOrder->load('employees');

        $jobOrderItems = $jobOrder->items;
        $jobOrderServices = $jobOrder->services;
        $jobOrderEmployees = $jobOrder->employees;

        $inventory = Inventory::all();
        $services = Service::all();
        $employees = Employee::all();

        return Inertia::render('JobOrder/Edit',[
            'jobOrders' => $jobOrder,
            'jobOrderItems' => $jobOrderItems,
            'jobOrderServices' => $jobOrderServices,
            'jobOrderEmployees' => $jobOrderEmployees,
            'inventories' => $inventory,
            'services' => $services,
            'employees' => $employees
        ]);
    }

    public function update(Request $request){
        $id = $request->jobOrderID;
        Validator::make($request->all(), [
            'vehicleDetails' => ['required', 'string'],
            'generalNotes' => ['nullable', 'string'],
            'jobOrderStatus' => ['required', 'string'],
            'netChange' => ['required', 'numeric'],
            'payment' => ['required', 'numeric']
        ])->validate();
        try{
            \DB::beginTransaction();
            //$originalJobOrder = JobOrder::find($id);
            $jobOrder = JobOrder::find($id);
            $jobOrder->vehicleDetails = $request->vehicleDetails;
            $jobOrder->generalNotes = $request->generalNotes;
            $jobOrder->jobOrderStatus = $request->jobOrderStatus;

            $jobOrder->increment('total', $request->input('netChange'));
            $jobOrder->increment('amountDue', $request->input('netChange'));
            $jobOrder->decrement('amountDue', $request->input('payment'));

            $total = $jobOrder->total;
            $balance = $jobOrder->amountDue;
            if($total == $balance){
                $jobOrder->paymentStatus = "Unpaid";
            }else if($balance == 0){
                $jobOrder->paymentStatus = "Fully Paid";
            }

            if($request->jobOrderStatus == 'Cancelled'){
                $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
                foreach($jobOrderServices as $jobOrderService){
                    $jobOrderService->amountPaid = 0;
                    $jobOrderService->save();
                }
            }

            if(($request->jobOrderStatus == 'Done' || $request->jobOrderStatus == 'Cancelled') && $jobOrder->paymentStatus == 'Fully Paid'){
                $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
                $serviceTotal = 0;
                foreach($jobOrderServices as $jobOrderService){
                    $serviceTotal += $jobOrderService->amountPaid;
                }
                $jobOrder->profit = $jobOrder->total - $serviceTotal;

                Finance::create([
                    'jobOrderID' => $id,
                    'amount' => $jobOrder->profit,
                    'transactionType' => 'Job Order',
                ]);
            }
            
            //If DONE, deduct quantity and, sum up total sales from items and services
            /*if($request->jobOrderStatus == 'Done'){
                $jobOrder->jobOrderStatus = $request->jobOrderStatus;
                //DECREMENT ITEM QUANTITY
                $jobOrderItems = JobOrderItem::where('jobOrderID', $id)->get();
                foreach ($jobOrderItems as $jobOrderItem) {
                    $itemID = $jobOrderItem->itemID;
                    
                    $item = Inventory::find($itemID);
                    if ($item) {
                        $item->decrement('itemQuantity', $jobOrderItem->quantityUsed);
                    }
                }
                //STORE TOTAL SALES
                $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
                foreach($jobOrderItems as $jobOrderItem){
                    $jobOrder->increment('totalSales', $jobOrderItem->amountPaid);
                }
                foreach($jobOrderServices as $jobOrderService){
                    $jobOrder->increment('totalSales', $jobOrderService->amountPaid);
                }
                */
            //If CANCELLED, deduct quantity and, sum up total sales from items used ONLY
            /*if($request->jobOrderStatus == 'Cancelled'){ 
                $jobOrder->jobOrderStatus = $request->jobOrderStatus;
                //DECREMENT ITEM QUANTITY
                $jobOrderItems = JobOrderItem::where('jobOrderID', $id)->get();
                foreach ($jobOrderItems as $jobOrderItem) {
                    $itemID = $jobOrderItem->itemID;
                    
                    $item = Inventory::find($itemID);
                    if ($item) {
                        $item->decrement('itemQuantity', $jobOrderItem->quantityUsed);
                    }
                }
                //STORE TOTAL SALES
                foreach($jobOrderItems as $jobOrderItem){
                    $jobOrder->increment('totalSales', $jobOrderItem->amountPaid);
                }
                //REMOVE AMOUNT PAID FROM SERVICES
                $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
                foreach($jobOrderServices as $jobOrderService){
                    $jobOrderService->amountPaid = 0;
                    $jobOrderService->save();
                }


            }*/

            $jobOrder->save();

            $historyData = $jobOrder->toArray();
            $historyData['jobOrderID'] = $jobOrder->id;
            $jobOrderHistory = JobOrderHistory::create($historyData);
    
            // Fetch the related data
            $jobOrderItems = JobOrderItem::where('jobOrderID', $id)->get();
            $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
            $jobOrderEmployees = JobOrderEmployee::where('jobOrderID', $id)->get();
    
            // Your existing code for updating related models
    
            /* Create history records for related models
            foreach ($jobOrderItems as $item) {
                $itemHistoryData = $item->toArray();
                $itemHistoryData['jobOrderItemID'] = $item->id;
                JobOrderItemHistory::create($itemHistoryData);
            }
    
            foreach ($jobOrderServices as $service) {
                $serviceHistoryData = $service->toArray();
                $serviceHistoryData['jobOrderServiceID'] = $service->id;
                JobOrderServiceHistory::create($serviceHistoryData);
            }
    
            foreach ($jobOrderEmployees as $employee) {
                $employeeHistoryData = $employee->toArray();
                $employeeHistoryData['jobOrderEmployeeID'] = $employee->id;
                JobOrderEmployeeHistory::create($employeeHistoryData);
            }
            */

            foreach ($jobOrderItems as $item) {
                $itemHistoryData = $item->toArray();
                $itemHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $itemHistoryData['jobOrderID'] = $jobOrder->id;
                JobOrderItemHistory::create($itemHistoryData);
            }
    
            foreach ($jobOrderServices as $service) {
                $serviceHistoryData = $service->toArray();
                $serviceHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $serviceHistoryData['jobOrderID'] = $jobOrder->id;
                JobOrderServiceHistory::create($serviceHistoryData);
            }
    
            foreach ($jobOrderEmployees as $employee) {
                $employeeHistoryData = $employee->toArray();
                $employeeHistoryData['joHistoryID'] = $jobOrderHistory->id;
                $employeeHistoryData['jobOrderID'] = $jobOrder->id;
                JobOrderEmployeeHistory::create($employeeHistoryData);
            }

            \DB::commit();

            Session::forget('jobOrderID'); //REMOVE?
            return redirect()->route('job_orders.index');
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving job order: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the job order.'], 500);
        }
    }

    public function pay(Request $request){
        $id = $request->jobOrderID;
        Validator::make($request->all(), [
            'customerPayment' => ['required', 'numeric']
        ])->validate();
        try{
            \DB::beginTransaction();
            $jobOrder = JobOrder::find($id);
            $customerPayment = $request->customerPayment;
            if($jobOrder->customerBalance !== 0){
                $jobOrder->decrement('customerBalance', $customerPayment);
            }
            if($jobOrder->customerBalance > 0 && $jobOrder->customerBalance < $jobOrder->total){
                $jobOrder->paymentStatus = "Partially Paid";
            }else if($jobOrder->customerBalance == 0){
                $jobOrder->paymentStatus = "Fully Paid";
            }

            if($jobOrder->jobOrderStatus == 'Done' && $jobOrder->paymentStatus == 'Fully Paid'){
                $jobOrderServices = JobOrderService::where('jobOrderID', $id)->get();
                $serviceTotal = 0;
                foreach($jobOrderServices as $jobOrderService){
                    $serviceTotal += $jobOrderService->amountPaid;
                }
                $jobOrder->profit = $jobOrder->total - $serviceTotal;
            }
            $jobOrder->save();
            
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving payment: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the payment.'], 500);
        }
    }

    public function history(Request $request){
        $id = $request->jobOrder;
        $joHistories = JobOrderHistory::where('jobOrderID', $id)->get();
        $joItemHistories = [];
        $joServiceHistories = [];
        $joEmployeeHistories = [];

        foreach($joHistories as $joHistory){
            $joItemHistory = JobOrderItemHistory::where('joHistoryID', $joHistory->id)->get();
            $joServiceHistory = JobOrderServiceHistory::where('joHistoryID', $joHistory->id)->get();
            $joEmployeeHistory = JobOrderEmployeeHistory::where('joHistoryID', $joHistory->id)->get();
            $joItemHistories = array_merge($joItemHistories, $joItemHistory->toArray());
            $joServiceHistories = array_merge($joServiceHistories, $joServiceHistory->toArray());
            $joEmployeeHistories = array_merge($joEmployeeHistories, $joEmployeeHistory->toArray());
        }
        $inventories = Inventory::all();
        $services = Service::all();
        $employees = Employee::all();
        return Inertia::render('JobOrder/History', [
            'job_order_history' => $joHistories,
            'job_order_item_history' => $joItemHistories,
            'job_order_service_history' => $joServiceHistories,
            'job_order_employee_history' => $joEmployeeHistories,
            'inventories' => $inventories,
            'services' => $services,
            'employees' => $employees
        ]);
    }

    /*public function refund(Request $request){
        $id = $request->jobOrderID;
        Validator::make($request->all(), [
            'customerRefund' => ['required', 'numeric']
        ])->validate();

        try{
            \DB::beginTransaction();
            $jobOrder = JobOrder::find($id);
            $customerRefund = $request->input('customerRefund');
            //$jobOrder->increment('customerBalance', $customerRefund);
            if($jobOrder->paymentStatus == "Fully Paid"){
                $jobOrder->increment('customerBalance', $customerRefund);
            };
            $jobOrder->save();
            \DB::commit();
        }catch (\Exception $e){
            \DB::rollBack();
            \Log::error("Error saving refund: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the refund.'], 500);
        }
    }*/
}