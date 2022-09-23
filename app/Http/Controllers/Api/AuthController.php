<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ClientError;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $service;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
        $this->service = new AuthService();
    }

    public function login(LoginRequest $request)
    {
        try{
            $tokenAndUser= $this->service->login($request->email, $request->password);

            return ResponseFormatter::success(
                [
                    'token_type' => 'Bearer',
                    'access_token' => $tokenAndUser['token'],
                    'user' => $tokenAndUser['user'],
                ],
                'Berhasil login'
            );

        }catch(Exception $e){
            if($e instanceof ClientError)
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    401
                );

            // server error
            Log::error($e->getMessage());
            return ResponseFormatter::error(
                null, 'Maaf, terjadi kegagalan pada server kami', 500
            );
        }


    }

    public function register(RegisterRequest $request){
        try{
            $tokenAndUser=$this->service->register(
                $request->name, $request->email, $request->password
            );

        return ResponseFormatter::success(
            [
                'token_type' => 'Bearer',
                'access_token' => $tokenAndUser['token'],
                'user' => $tokenAndUser['user'],
            ],
            'Berhasil Registrasi'
        );
        }catch(Exception $e){
            if($e instanceof ClientError)
                return ResponseFormatter::error(
                    null,
                    $e->getMessage(),
                    401
                );

            // server error
            Log::error($e->getMessage());
            return ResponseFormatter::error(
                null, 'Maaf, terjadi kegagalan pada server kami', 500
            );
        }
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
