<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\OtpVerification;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        private EmailService $emailService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'status' => 'active',
        ];
        
        cache()->put("registration_data_{$request->email}", $userData, now()->addMinutes(15));
        
        $otp = OtpVerification::generateOtp($request->email, 'registration');
        
        $this->emailService->sendOtp($request->email, $otp->otp, 'registration');

        return response()->json([
            'message' => 'Registration initiated. Please check your email for OTP verification.',
            'email' => $request->email,
        ], 200);
    }

    public function verifyRegistration(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if (!OtpVerification::verify($request->email, $request->otp, 'registration')) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $userData = cache()->get("registration_data_{$request->email}");
        
        if (!$userData) {
            return response()->json([
                'message' => 'Registration data not found. Please start registration again.',
            ], 422);
        }

        $user = User::create($userData);
        
        $customerRole = \App\Models\Role::where('name', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        cache()->forget("registration_data_{$request->email}");

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registration successful.',
            'user' => $user->load('roles'),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = auth()->user();

        if ($user->status !== 'active') {
            return response()->json([
                'message' => 'Your account has been blocked. Please contact support.',
            ], 403);
        }

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 200);
    }

    public function logout(): JsonResponse
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Successfully logged out.',
        ], 200);
    }

    public function refresh(): JsonResponse
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], 200);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => auth()->user()->load('roles'),
        ], 200);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ]);

        $otp = OtpVerification::generateOtp($request->email, 'password_reset');
        
        $this->emailService->sendOtp($request->email, $otp->otp, 'password_reset');

        return response()->json([
            'message' => 'Password reset OTP sent to your email.',
        ], 200);
    }

    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        if (!OtpVerification::verify($request->email, $request->otp, 'password_reset')) {
            return response()->json([
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password reset successful. You can now login with your new password.',
        ], 200);
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'type' => ['required', 'string', 'in:registration,password_reset,email_verification'],
        ]);

        $otp = OtpVerification::generateOtp($request->email, $request->type);
        
        $this->emailService->sendOtp($request->email, $otp->otp, $request->type);

        return response()->json([
            'message' => 'OTP resent successfully.',
        ], 200);
    }
}
