<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Http\Requests\TableRequest;
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
                'message' => 'Login failed. ' . $e->getMessage()
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
                'message' => 'Session started',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start session',
            ], 500);
        }
    }



    public function endSession()
    {
        $tableId = session('tableId');

        if (!session()->has('tableId')) {

            return response()->json([
                'success' => false,
                'message' => 'No sesions found for ' . $tableId
            ]);
        }
        session()->flush();

        return response()->json([
            'success' => true,
            'message' => 'Session ended successfully',
        ], 200);
    }

}
