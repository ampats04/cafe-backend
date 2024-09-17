<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\TableRequest;
use App\Models\Orders;
use App\Models\Table;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    // Inject AuthService via constructor
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user login.
     */
    public function login(AuthRequest $request)
    {
        try {
            // Use the AuthService to attempt login
            $user = $this->authService->login($request);

            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful!',
                    'user' => $user
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.'
            ], 401);
        } catch (Exception $e) {
            // Handle any exceptions that occur in the service
            return response()->json([
                'success' => false,
                'message' => 'Login failed.'
            ], 500);
        }
    }

    /**
     * Handle user logout.
     */
    public function logout()
    {
        // Return a simple logout success message
        return response()->json([
            'success' => true,
            'message' => 'Logout successful!'
        ], 200);
    }

    public function startSession(TableRequest $request)
    {

        $table_inactive = Table::where('tableNumber', $request->tableNumber)
            ->where('status', 'Inactive')
            ->first();

        if (!$table_inactive) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect table number or table is taken',
            ], 404);
        }

        $table_active = Table::where('tableNumber', $request->tableNumber)
            ->where('status', 'Active')
            ->first();

        if ($table_active) {
            return response()->json([
                'success' => false,
                'message' => 'Table is already taken'
            ]);
        }

        $table_inactive->update(['status' => 'Active']);
        $pkTableId = $table_inactive->pkTableId;

        session()->put('tableId', $pkTableId);

        if (session()->has('tableId')) {
            return response()->json([
                'success' => true,
                'tableId' => session('tableId'),
                'message' => 'Welcome to Tea Crate Cafe!',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }



    public function endSession(TableRequest $request)
    {
        $tableId = session('tableId');



        $table_id = $request->fkTableId;
        $table = Table::where('pkTableId', '=', $table_id)
            ->where('status', 'Active')
            ->first();

        $cartItems = Orders::where('fkTableId', $table_id)
            ->pending()
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'Cart is empty'], 400);
        }

        foreach ($cartItems as $cartItem) {
            $cartItem->update(['status' => 'Completed']);
        }


        $table->update(['status' => 'Inactive']);
        session()->flush();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for dining in!',
        ], 200);
    }

}
