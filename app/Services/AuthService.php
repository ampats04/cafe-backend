<?php

namespace App\Services;

use App\Http\Requests\TableRequest;
use App\Models\Table;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthService
{
    public function login(Request $request)
    {
        try {
            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Verify the password
            if ($user && Hash::check($request->password, $user->password)) {
                // If successful, return user data
                return $user;
            }

            // If login fails, return false
            return false;
        } catch (Exception $e) {
            // Handle any unexpected errors
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }



}
