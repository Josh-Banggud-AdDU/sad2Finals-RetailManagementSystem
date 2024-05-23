<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalesItemController;
use App\Http\Controllers\InventoryLogController;
use App\Http\Controllers\InventoryLogItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\JobOrderController;
use App\Http\Controllers\JobOrderItemController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CashOnHandController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\JobOrderServiceController;
use App\Http\Controllers\JobOrderEmployeeController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['role:1'])->group(function(){
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::post('/employees/store', [EmployeeController::class, 'store'])->name('employees.store');

    Route::resource('finances', FinanceController::class)->except(['show']);
    Route::get('/finances/main', [FinanceController::class, 'main'])->name('finances.main')->middleware('role:1');
    Route::get('/finances/index', [FinanceController::class, 'index'])->name('finances.index');
    Route::get('/finances/print', [FinanceController::class, 'print'])->name('finances.print');

    Route::get('/cash_on_hands', [CashOnHandController::class, 'index'])->name('cash_on_hands.index');
    Route::get('/cash_on_hands/start', [CashOnHandController::class, 'start'])->name('cash_on_hands.start');
    Route::get('/cash_on_hands/end', [CashOnHandController::class, 'end'])->name('cash_on_hands.end');
    Route::post('/cash_on_hands/store', [CashOnHandController::class, 'store'])->name('cash_on_hands.store');
    Route::post('/cash_on_hands/update', [CashOnHandController::class, 'update'])->name('cash_on_hands.update');
    Route::get('/web/cash_on_hands', [CashOnHandController::class, 'index']);
    Route::post('/cash_on_hands/edit', [CashOnHandController::class, 'edit'])->name('cash_on_hands.edit'); //might not need this(?)
    Route::get('/check-cash-on-hand', 'CashOnHandController@checkForToday');

    Route::get('/cashflows/create', [CashflowController::class, 'create'])->name('cashflows.create');
    Route::post('/cashflows/store', [CashflowController::class, 'store'])->name('cashflows.store');
    Route::get('/cashflows/{id}/edit', [CashflowController::class, 'edit'])->name('cashflows.edit'); //might not need this(?)
    Route::put('/cashflows/{id}', [CashflowController::class, 'update'])->name('cashflows.update'); //might not need this(?)
});

Route::middleware(['role:1,2'])->group(function () {
    Route::resource('inventories', InventoryController::class)->except(['show']);
    Route::get('/inventories/main', [InventoryController::class, 'main'])->name('inventories.main');
    Route::get('/inventories/manage_supply', [InventoryController::class, 'manage_supply'])->name('inventories.manage_supply');
    Route::get('/show_units', [InventoryController::class, 'showUnits']);
    Route::get('/show_categories', [InventoryController::class, 'showCategories']);

    Route::resource('sales', SaleController::class)->except(['show']);
    Route::get('/sales/main', [SaleController::class, 'main'])->name('sales.main');
    Route::post('/sales/store', [SaleController::class, 'store'])->name('sales.store');
    //Route::get('/sales/edit/{sales}', [SaleController::class, 'edit'])->name('sales.edit');

    Route::post('/sales_items/store', [SalesItemController::class, 'store'])->name('sales_items.store');
    //Route::post('/sales_items/update', [SalesItemController::class, 'update'])->name('sales_items.update');
    
    Route::resource('supply_management', InventoryLogController::class)->except(['show']);
    Route::get('/supply_management/stock_in', [InventoryLogController::class, 'stock_in'])->name('inventory_logs.stock_in');
    Route::get('/supply_management/stock_out', [InventoryLogController::class, 'stock_out'])->name('inventory_logs.stock_out');
    Route::get('/supply_management/item_return', [InventoryLogController::class, 'item_return'])->name('inventory_logs.item_return');
    Route::post('/supply_management/store', [InventoryLogController::class, 'store'])->name('inventory_logs.store');
    //Route::get('/supply_management/edit/{inventoryLog}', [InventoryLogController::class, 'edit'])->name('inventory_logs.edit');

    Route::post('/supply_management_items/store', [InventoryLogItemController::class, 'store'])->name('inventory_logs_items.store');
    Route::resource('suppliers', SupplierController::class);
});

Route::get('/search_inventories', [InventoryController::class, 'search']);
Route::get('/search_suppliers', [SupplierController::class, 'search']);
Route::get('/search_employees', [EmployeeController::class, 'search']);
//Route::get('/search', [InventoryController::class, 'searchItemOrSupplier']);

Route::get('/services/index', [ServiceController::class, 'index'])->name('services.index');
Route::post('/services/store', [ServiceController::class, 'store'])->name('services.store');
Route::get('/search_services', [ServiceController::class, 'search']);

Route::resource('job_orders', JobOrderController::class)->except(['show']);
Route::get('/job_orders/main', [JobOrderController::class, 'main'])->name('job_orders.main');
Route::post('/job_orders/store', [JobOrderController::class, 'store'])->name('job_orders.store');
Route::get('/job_orders/edit/{jobOrder}', [JobOrderController::class, 'edit'])->name('job_orders.edit');
Route::post('/job_orders/update', [JobOrderController::class, 'update'])->name('job_orders.update');
Route::get('/job_orders/history', [JobOrderController::class, 'history'])->name('job_orders.history');
//Route::post('/job_orders/pay', [JobOrderController::class, 'pay'])->name('job_orders.pay');
//Route::post('/job_orders/refund', [JobOrderController::class, 'refund'])->name('job_orders.refund');

Route::post('/job_order_items/store', [JobOrderItemController::class, 'store'])->name('job_order_items.store');
Route::post('/job_order_items/update', [JobOrderItemController::class, 'update'])->name('job_order_items.update');

Route::post('/job_order_services/store', [JobOrderServiceController::class, 'store'])->name('job_order_services.store');
Route::post('/job_order_services/update', [JobOrderServiceController::class, 'update'])->name('job_order_services.update');

Route::post('/job_order_employees/store', [JobOrderEmployeeController::class, 'store'])->name('job_order_employees.store');
Route::post('/job_order_employees/update', [JobOrderEmployeeController::class, 'update'])->name('job_order_employees.update');

Route::get('/images/{filename}', function ($filename) {
    $path = public_path("images/$filename");
    return response()->file($path);
})->where('filename', '.*');



