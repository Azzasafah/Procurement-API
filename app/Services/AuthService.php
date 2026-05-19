<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function register(array $data)
    {
        $user = User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password'      => Hash::make($data['password']),
            'department_id' => $data['department_id'] ?? null,
            'role'          => $data['role'] ?? User::ROLE_EMPLOYEE,
            'phone'         => $data['phone'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('department'),
        ];
    }

    public function login(string $email, string $password)
    {

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return null;
        }

        $user  = User::where('email', $email)->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load('department')
        ];
    }
}
