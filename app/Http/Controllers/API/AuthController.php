<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $authservice) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authservice->register($request->validated());
            return ResponseFormatter::success($result, 'Registrasi berhasil.', 201);
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authservice->login($request->email, $request->password);
            if (!$result) {
                return ResponseFormatter::error('Email atau password salah', 401);
            }
            return ResponseFormatter::success($result, 'login berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return ResponseFormatter::success(null, 'Logout berhasil');
        } catch (Exception $e) {
            return ResponseFormatter::error($e->getMessage(), 500);
        }
    }

    public function me(Request $request)
    {
        return ResponseFormatter::success($request->user()->load('department'), 'User data retrieved');
    }
}
