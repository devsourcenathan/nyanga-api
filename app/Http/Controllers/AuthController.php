<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Upload;

class AuthController extends Controller
{
    use OTPHandler;

    const ROLE_CLIENT = 'CLIENT';
    const ROLE_PROVIDER = 'PROVIDER';
    const STATUS_INACTIVE = false;
    const STATUS_ACTIVE = true;

    public function register(Request $request)
    {
        $this->validateSignupRequest($request);
        return $this->createUserAndSendOTP($request, $request->role ?? self::ROLE_CLIENT);
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
            return $this->handleException($e);
        }
    }

    public function signupProvider(Request $request)
    {
        $this->validateSignupRequest($request);
        return $this->createUserAndSendOTP($request, self::ROLE_PROVIDER);
    }

    public function signupClient(Request $request)
    {
        $this->validateSignupRequest($request);
        return $this->createUserAndSendOTP($request, self::ROLE_CLIENT);
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

    private function validateSignupRequest(Request $request)
    {
        return $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'telephone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);
    }

    private function createUserAndSendOTP(Request $request, $role)
    {
        try {
            $user = $this->createUser($request->all(), $role);
            $this->sendOTPEmail($user->email, $user->otp);

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    private function createUser(array $data, $role)
    {
        $otp = $this->generateOTP();
        $logo = Upload::uploadFile($data['logo']);
        return User::create(
            array_merge(
                $data,
                [
                    'password' => Hash::make($data['password']),
                    'role' => $role,
                    'status' => self::STATUS_INACTIVE,
                    'otp' => $otp,
                    'logo' => $logo
                ]
            )
        );
    }

    private function generateOTP()
    {
        return mt_rand(1000, 9999);
    }

    private function sendOTPEmail($email, $otp)
    {
        Mail::to($email)->send(new OTPMail($otp));
    }

    private function handleException(\Exception $e)
    {
        if ($e instanceof ValidationException && isset($e->errors()['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cet email existe déjà.',
            ], 400);
        }
        return 'An error occurred. Please try again later.';
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], $e instanceof ValidationException ? 400 : 500);
    }
}

trait OTPHandler
{
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
                'status' => AuthController::STATUS_ACTIVE,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
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

            $otp = $this->generateOTP();

            $this->sendOTPEmail($request->email, $otp);

            $user->update([
                'reset_password_otp' => $otp,
                'reset_password_expires' => now()->addHour(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reset OTP sent successfully',
            ], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
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
            return $this->handleException($e);
        }
    }
}
