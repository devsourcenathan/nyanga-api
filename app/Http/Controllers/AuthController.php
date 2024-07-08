<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $user = Auth::user();
    //         $token = $user->createToken('authToken')->plainTextToken;

    //         return response()->json(['token' => $token], 200);
    //     } else {
    //         return response()->json(['error' => 'Unauthorized'], 401);
    //     }
    // }

    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function user(Request $request)
    {
        $user = $request->user(); // Utilisateur authentifié
        return response()->json($user);
    }

    public function update_password(Request $request, $id)
    {
        $user = User::find($id);

        $user->password = bcrypt($request->password);

        return response()->json($user->save());
    }
    public function users(Request $request)
    {
        return DB::table("users")->get(["email", "name", "id", "role", "permissions", "service_id"]);
    }

    public function registerProvider(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // Add other fields validation as per your requirements
        ]);

        try {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'PROVIDER',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Register a customer
    public function registerCustomer(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            // Add other fields validation as per your requirements
        ]);

        try {
            $otp = mt_rand(1000, 9999); // Generate OTP

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'CLIENT',
                'status' => false,
                'otp' => $otp,
            ]);

            // Send OTP email
            // Mail::to($request->email)->send(new OTPMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Verify OTP for user registration
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw new \Exception('User not found');
            }

            if ($user->otp != $request->otp) {
                throw new \Exception('Invalid OTP');
            }

            // Update user status and clear OTP
            $user->update([
                'otp' => null,
                'status' => true,
            ]);

            // Send welcome email
            // Mail::to($user->email)->send(new WelcomeMail());

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Login user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = auth()->user()->createToken('AuthToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'user' => auth()->user(),
        ], 200);
    }

    // Forgot password: Generate OTP and send reset email
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                throw new \Exception('User not found');
            }

            $otp = mt_rand(1000, 9999); // Generate OTP

            // Update user with OTP and expiry time
            $user->update([
                'reset_password_otp' => $otp,
                'reset_password_expires' => now()->addHour(),
            ]);

            // Send OTP reset email
            // Mail::to($user->email)->send(new ResetPasswordMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'Reset OTP sent successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Reset password: Verify OTP and update password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6',
        ]);

        try {
            $user = User::where('email', $request->email)
                ->where('reset_password_otp', $request->otp)
                ->where('reset_password_expires', '>=', now())
                ->first();

            if (!$user) {
                throw new \Exception('Invalid or expired OTP');
            }

            // Update user password and clear OTP
            $user->update([
                'password' => Hash::make($request->password),
                'reset_password_otp' => null,
                'reset_password_expires' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
