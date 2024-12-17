<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try{
            $admin = Admin::where('email', $request->email)->first();

            if(!$admin || !Hash::check($request->password, $admin->password)){
                return response()->json([
                    'message' => 'Invalid credentials.'
                ], 401);
            }

            $token = $admin->createToken('admin-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
                'admin' => $admin
            ], 200);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong'], 500);
        }
        
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ]);
    }
}
