<?php

namespace App\Services;

use App\Http\Requests\TableRequest;
use App\Models\Admin;
use App\Models\Table;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthService
{
    public function login($request)
    {
        try {
            $user = Admin::where('email', $request->email)
                ->first();

            // Verify the password
            if ($user && Hash::check($request->password, $user->password)) {
                return $user;
            }

            return false;
        } catch (Exception $e) {
            // Handle any unexpected errors
            throw new Exception('Login failed: ' . $e->getMessage());
        }
    }



}
