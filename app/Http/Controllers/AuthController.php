<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $otp = mt_rand(1000, 9999);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 409);
            }

            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'surname' => $request->surname,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'address' => $request->address,
                'logo' => $request->logo,
                'description' => $request->description,
                'role' => $request->role ?? 'CLIENT',
                'status' => false,
                'otp' => $otp
            ]);

            Mail::to($request->email)->send(new OTPMail($otp));

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token], 201);
        } catch (ValidationException $e) {
            if ($e->errors() && isset($e->errors()['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 400);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function update_password(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::findOrFail($id);

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'Old password does not match'], 400);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Password updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function signupProvider(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        try {
            $otp = mt_rand(1000, 9999);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 409);
            }

            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'surname' => $request->surname,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'address' => $request->address,
                'logo' => $request->logo,
                'description' => $request->description,
                'role' => 'PROVIDER',
                'status' => false,
                'otp' => $otp,
            ]);


            // Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($request) {
            //     $message->to($request->email)->subject('Votre code OTP');
            // });
            Mail::to($request->email)->send(new OTPMail($otp));


            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $user,
            ], 201);
        } catch (ValidationException $e) {
            if ($e->errors() && isset($e->errors()['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 400);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function signupClient(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'password' => 'required|string|min:6'
        ]);

        try {
            $otp = mt_rand(1000, 9999);

            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 409);
            }

            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'surname' => $request->surname,
                'password' => Hash::make($request->password),
                'telephone' => $request->telephone,
                'address' => $request->address,
                'logo' => $request->logo,
                'description' => $request->description,
                'role' => 'CLIENT',
                'status' => false,
                'otp' => $otp,
            ]);


            Mail::to($request->email)->send(new OTPMail($otp));

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $user,
            ], 201);
        } catch (ValidationException $e) {
            if ($e->errors() && isset($e->errors()['email'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cet email existe déjà.',
                ], 400);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

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

            $user->update([
                'otp' => null,
                'status' => true,
            ]);

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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
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

            $otp = mt_rand(1000, 9999);

            Mail::to($request->email)->send(new OTPMail($otp));

            $user->update([
                'reset_password_otp' => $otp,
                'reset_password_expires' => now()->addHour(),
            ]);

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

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|string|min:6',
        ]);

        try {
            $user = User::where('email', $request->email)
                ->where('reset_password_otp', $request->otp)
                ->where('reset_password_expires', '>=', now())
                ->first();

            if (!$user) {
                throw new \Exception('Invalid or expired OTP');
            }

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
