<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Cashflow;
use App\Models\Finance;
use Illuminate\Support\Facades\Validator;

class CashflowController extends Controller
{
    public function create()
    {
        // Return a view for creating a new cashflow
        $cashflow = Cashflow::all(); // Or use any logic to get the data you need
        return Inertia::render('Finance/Create', [
            'cashflows' => $cashflow,
        ]);
    }


    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'amount' => 'required',
            'referenceNumber' => 'required',
            'generalNotes' => 'nullable', // Allowing optional generalNotes
            'transactionType' => 'required',
        ]);

        $amount = $request->amount;
        if ($request->transactionType === 'Expense' || $request->transactionType === 'Disbursement' || $request->transactionType === 'Cash Out') {
            // If it's an expense or disbursement, make the amount negative
            $amount = -$amount;
        }    

        // Create a new cashflow in the database
        $cashflow = Cashflow::create($request->all());

        // Automatically handle the finance record creation in the model event
        Finance::create([
            'cashflowID' => $cashflow->id,
            'amount' => $amount,
            'transactionType' => $cashflow->transactionType,
            'generalNotes' => $cashflow->generalNotes,
        ]);

        // Redirect to the index page or a success page
        return redirect()->route('finances.index');
    }

}
