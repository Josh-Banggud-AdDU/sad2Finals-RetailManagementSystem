<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\CashOnHand;
use App\Models\CashOnHandHistory;
use Illuminate\Support\Facades\Validator;

class CashOnHandController extends Controller
{
    public function index()
    {
        $cashOnHand = CashOnHand::all();
        return Inertia::render('Finance/Cash_On_Hand', ['cashOnHand' => $cashOnHand]);
    }

    public function start()
    {
        $cashOnHand = CashOnHand::all();
        return Inertia::render('Finance/Cash_On_Hand_Create', ['cashOnHand' => $cashOnHand]);
    }

    public function end()
    {
        $cashOnHand = CashOnHand::latest()->first();
        return Inertia::render('Finance/Edit', ['cashOnHand' => $cashOnHand]);
    }

    public function store(Request $request)
    {
        $cashOnHand = CashOnHand::create($request->all());
        CashOnHandHistory::create($request->all() + ['cash_on_hand_id' => $cashOnHand->id]);

        return redirect()->route('cash_on_hands.index'); // Redirect to the appropriate route
    }

    public function update(Request $request)
    {
        $cashOnHand = CashOnHand::latest()->first();
        $cashOnHand->update($request->all());

        return redirect()->route('cash_on_hands.index'); // Redirect to the appropriate route
    }

    public function checkForToday()
    {
        $currentDate = Carbon::now()->toDateString();
        $recordExists = CashOnHand::whereDate('created_at', $currentDate)->exists();

        return response()->json(['exists' => $recordExists]);
    }
}
