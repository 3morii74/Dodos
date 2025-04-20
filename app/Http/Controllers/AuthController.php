<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\VerifyResetCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Services\ResetPasswordMail;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\ApiError;

class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Hashed via mutator
            'slug' => Str::slug($request->name),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'data' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Incorrect email or password'], 401);
        }

        $user = auth('api')->user();

        return response()->json([
            'data' => $user,
            'token' => $token,
        ]);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $resetCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $hashedResetCode = hash('sha256', $resetCode);

        $user->update([
            'password_reset_code' => $hashedResetCode,
            'password_reset_expires' => now()->addMinutes(10),
            'password_reset_verified' => false,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user->name, $resetCode));
        } catch (\Exception $e) {
            $user->update([
                'password_reset_code' => null,
                'password_reset_expires' => null,
                'password_reset_verified' => false,
            ]);
            throw new ApiError('There is an error in sending email', 500);
        }

        return response()->json(['message' => 'Reset code sent to email']);
    }

    public function verifyResetCode(VerifyResetCodeRequest $request)
    {
        $hashedResetCode = hash('sha256', $request->resetCode);

        $user = User::where('password_reset_code', $hashedResetCode)
            ->where('password_reset_expires', '>', now())
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Reset code invalid or expired'], 400);
        }

        $user->update(['password_reset_verified' => true]);

        return response()->json(['message' => 'Reset code verified']);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)
            ->where('password_reset_verified', true)
            ->first();

        if (!$user) {
            return response()->json(['error' => 'Reset code not verified or user not found'], 400);
        }

        $user->update([
            'password' => $request->newPassword, // Hashed via mutator
            'password_reset_code' => null,
            'password_reset_expires' => null,
            'password_reset_verified' => false,
            'password_changed_at' => now(),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token]);
    }
}
