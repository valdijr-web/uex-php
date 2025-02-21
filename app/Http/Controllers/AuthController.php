<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Registro do usuário.
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:5|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken($user->name . 'authToken')->plainTextToken;
            return response()->json([
                'message' => 'Usuário registrado com sucesso.',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha ao registrar usuário.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Login do usuário.
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'errors' => $validator->errors()
                    ],
                    422
                );
            }
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Credenciais inválidas.'], 401);
            }
            $token = $user->createToken($user->name . ' - authToken')->plainTextToken;
            return response()->json([
                'message' => 'Usuário logado com sucesso.',
                'user' => $user,
                'token' => $token
            ], 200);
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

    public function sendPasswordResetLink(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $status = Password::sendResetLink(
                $request->only('email')
            );
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => 'Link para redefinição de senha enviado por email.'], 200);
            } else {
                return response()->json(['email' => 'Não foi possível enviar o link de redefinição.'], 422);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha na solicitação de redefinição de senha.', 'error' => $e->getMessage()], 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:5|confirmed'
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->setRememberToken(Str::random(60));
                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => 'Senha redefinida com sucesso.'], 200);
            } else {
                return response()->json(['email' => 'Oops! Token ou e-mail inválido.'], 400);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Falha na redefinição da senha.', 'error' => $e->getMessage()], 500);
        }
    }
}
