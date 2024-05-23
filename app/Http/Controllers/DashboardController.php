<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\JobOrder;
use App\Models\Supplier;

use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index()
    {
        // Return a view for creating a new cashflow
        $inventories = Inventory::all();
        $sales = Sale::all();
        $jobOrders = JobOrder::all();
        $suppliers = Supplier::all();

        return Inertia::render('Dashboard', [
            'inventories' => $inventories,
            'sales' => $sales,
            'jobOrders' => $jobOrders,
            'suppliers' => $suppliers
        ]);
    }
}
