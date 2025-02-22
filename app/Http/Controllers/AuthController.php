<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordSendEmailRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Str;

class AuthController extends Controller
{

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Registro do usuário.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            
            $response = $this->authService->register($request->validated());
            return response()->json($response, 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha ao registrar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Login do usuário.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->login($request->email, $request->password);
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha ao realizar login', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logout do usuário.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Usuário deslogado com sucesso.'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha ao sair.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Enviar link de redefinição de senha.
     */

    public function sendPasswordResetLink(PasswordSendEmailRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->sendResetLink($request->email);
            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha na solicitação de redefinição de senha.', 'error' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(PasswordResetRequest $request): JsonResponse
    {
        try {
            $response = $this->authService->resetPassword($request->only(
                'email', 'password', 'password_confirmation', 'token'
            ));

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha na redefinição da senha.', 'error' => $e->getMessage()], 500);
        }
    }
}
