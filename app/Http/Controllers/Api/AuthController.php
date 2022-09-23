<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => $user,
            ],
            'Berhasil login'
        );

    }

    public function register(RegisterRequest $request){
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::guard('api')->login($user);
        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => $token,
                'user' => $user,
            ],
            'Berhasil mendaftar'
        );
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return ResponseFormatter::success(null, 'Berhasil logout');
    }

    public function refresh()
    {
        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => Auth::guard('api')->refresh(),
            ],
            'Berhasil refresh token'
        );
    }
}
