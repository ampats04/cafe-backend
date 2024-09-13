<?php

namespace App\Http\Controllers;

use App\Http\Requests\TableRequest;
use App\Models\Table;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function addCustomerName(TableRequest $request)
    {

        $tableId = session('tableId');


        if (!session()->has('tableId')) {

            return response()->json([
                'success' => false,
                'message' => 'No sesions found for ' . $tableId
            ]);
        }

        $table = Table::find($tableId);
        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Table not found. Please double-check your table number.'
            ], 404); // Not found
        }

        $table->customer_name = $request->customerName;
        $table->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer name updated successfully.',
            'table' => $table
        ], 200); // Success
    }

}
