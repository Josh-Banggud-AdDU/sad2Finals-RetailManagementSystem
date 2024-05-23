<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Finance;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\JobOrderService;
use App\Models\JobOrderEmployee;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Cashflow;
use App\Models\CashOnHand;
use App\Models\CashOnHandHistory;
use App\Models\Inventory;

class FinanceController extends Controller
{
    /**
     * Display main page for Finance
     *
     * @return \Inertia\Response
     */
    public function main()
    {
        // Retrieve the total cash on hand
         $cashOnHand = CashOnHand::first();
        $totalCashOnHand = $cashOnHand ? $cashOnHand->totalCashOnHand : 0;

        return Inertia::render('Finance/Main', ['totalCashOnHand' => $totalCashOnHand]);
    }

    public function index()
    {
        $finances = Finance::all();
        $sales = Sale::all();
        $sale_items = SalesItem::all();
        $job_order_items = JobOrderItem::all();
        $job_order_employees = JobOrderEmployee::all();
        $job_order_services = JobOrderService::all();
        $job_orders = JobOrder::all();
        $cashflows = Cashflow::all();
        $cash_on_hands = CashOnHand::all();
        $inventories = Inventory::all();
        $cash_on_hand_histories = CashOnHandHistory::all();

        return Inertia::render('Finance/Index', 
        [
            'finances' => $finances, 
            'sales' => $sales,
            'sale_items' => $sale_items, 
            'job_orders' => $job_orders,
            'job_order_items' => $job_order_items,
            'job_order_employees' => $job_order_employees,
            'job_order_services' => $job_order_services, 
            'cashflows' => $cashflows, 
            'cash_on_hands' => $cash_on_hands,
            'cash_on_hand_histories' => $cash_on_hand_histories,
            'inventories' => $inventories
        ]);
    }

    /**
     * Display the specified finance record and its related items and services.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $finance = Finance::findOrFail($id);

        // Load related data
        $salesItems = $finance->salesOrder->items;
        $jobOrderItems = $finance->jobOrder->items;
        $jobOrderServices = $finance->jobOrder->services;

        return Inertia::render('Finance/Show', [
            'finance' => $finance,
            'salesItems' => $salesItems,
            'jobOrderItems' => $jobOrderItems,
            'jobOrderServices' => $jobOrderServices,
        ]);
    }

    public function print(){
        $finances = Finance::all();
        $cash_on_hands = CashOnHand::all();
        $cash_on_hand_histories = CashOnHandHistory::all();

        return Inertia::render('Finance/Printer', [
            'finances' => $finances,
            'cash_on_hands' => $cash_on_hands,
            'cash_on_hand_histories' => $cash_on_hand_histories,
        ]);
    }
}
