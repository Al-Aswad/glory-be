<?php

namespace App\Services;

use App\Exceptions\InvariantError;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Termwind\Components\Dd;

interface AuthServiceInterface
{
    public function login($email, $password);
    public function register($name, $email, $password);
}

class AuthService implements AuthServiceInterface{
    public function login($email, $password){

        $token = Auth::guard('api')->attempt([
            'email' => $email,
            'password' => $password
        ]);

        if(!$token)
            throw new InvariantError('Email atau password salah');

        return [
            'token' => $token,
            'user' => Auth::guard('api')->user()

        ];
    }

    public function register($name, $email, $password){
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $token = Auth::guard('api')->attempt([
            'email' => $email,
            'password' => $password
        ]);

        return [
            'token' => $token,
            'user' => $user
        ];
    }
}


;?>
