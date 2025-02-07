<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function register(RegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $start = '====== Start register ======';
        $end = '====== End register ======';
        try {
            Log::info($start);
            DB::beginTransaction();


            if(User::where('email', $request->input('email'))->exists()){
                Log::error('The email has already been taken.');
                Log::info($end);
                return response()->json([
                    'message' => 'The email has already been taken.'
                ], 400);
            }

            // Create a new user and hash the password
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile_number' => $request->mobile_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            Log::info('User created successfully');

            // Generate JWT token for the user
            $token = JWTAuth::fromUser($user);
            DB::commit();
            Log::info($end);

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Exception : ' . $e->getMessage());
            Log::info($end);
            return response()->json([
                'message' => 'User creation failed'
            ], 400);
        }
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $start = '====== Start login ======';
        $end = '====== End login ======';
        try {
            Log::info($start);
            // Validate email and password
            $credentials = $request->only('email', 'password');

            // Try to authenticate the user using JWTAuth
            if (!$token = JWTAuth::attempt($credentials)) {
                Log::info('Unauthorized');
                Log::info($end);
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }

            Log::info($end);
            return $this->respondWithToken($token);
        } catch (Exception $e) {
            Log::error('Exception : ' . $e->getMessage());
            Log::info($end);
            return response()->json([
                'message' => 'Login failed'
            ], 500);
        }
    }

    // Format JWT Token Response
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60  // Token lifetime in seconds
        ]);
    }


    public function logout(Request $request)
    {
        $start = '====== Start logout ======';
        $end = '====== End logout ======';
        try {
            Log::info($start);
            JWTAuth::invalidate(JWTAuth::getToken());

            Log::info($end);
            return response()->json([
                'message' => 'User successfully logged out.',
            ]);
        } catch (JWTException $e) {
            Log::info('Exception : ' . $e->getMessage());
            Log::info($end);
            return response()->json([
                'error' => 'Failed to logout. Please try again.',
            ], 500);
        }
    }

    public function user(): \Illuminate\Http\JsonResponse
    {
        $start = '====== Start user info ======';
        $end = '====== End user info ======';
        try {
            Log::info($start);
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }
            Log::info($end);
            return response()->json($user);
        } catch (Exception $e) {
            Log::error('Exception : ' . $e->getMessage());
            Log::info($end);
            return response()->json([
                'message' => 'Unable to fetch user information'
            ], 500);
        }
    }
}
