<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $service;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->service = new AuthService();
    }

    public function login(LoginRequest $request)
    {
        $tokenAndUser = $this->service->login($request->email, $request->password);

        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => $tokenAndUser['token'],
                'user' => $tokenAndUser['user'],
            ],
            'Berhasil login'
        );
    }

    public function register(RegisterRequest $request)
    {
        $tokenAndUser = $this->service->register(
            $request->name,
            $request->email,
            $request->password
        );

        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => $tokenAndUser['token'],
                'user' => $tokenAndUser['user'],
            ],
            'Berhasil Registrasi'
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
