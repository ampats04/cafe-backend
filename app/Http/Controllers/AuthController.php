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
                    'data' => $user
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
                'message' => 'Login failed'
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


        try {
            $table_inactive = Table::where('tableNumber', $request->tableNumber)
                ->inactive()
                ->first();

            if (!$table_inactive) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect table number. Please input correct table number',
                ], 404);
            }

            $table_active = Table::where('tableNumber', $request->tableNumber)
                ->active()
                ->first();

            if ($table_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table is already taken'
                ]);
            }

            $table_inactive->update(['status' => 'Active']);
            // $pkTableId = $table_inactive->pkTableId;

            // session()->put('tableId', $pkTableId);

            return response()->json([
                'success' => true,
                'message' => 'Welcome to Tea Crate Cafe!',

            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }


    }

    public function endSession($tableId)
    {
        try {
            $table = Table::where('pkTableId', '=', $tableId)
                ->where('status', 'Active')
                ->first();
    
            $cartItems = Orders::where('fkTableId', $tableId)
                ->served()
                ->get();
    
            if ($cartItems->isNotEmpty()) {
                foreach ($cartItems as $cartItem) {
                    $cartItem->update(['status' => 'Completed']);
                }
            }
    
            // Update table status
            $table->update(['status' => 'Inactive', 'customerName' => null]);
    
            return response()->json([
                'success' => true,
                'message' => 'Thank you for dining in!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
    

}
