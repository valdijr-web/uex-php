<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function deleteAccount(DeleteAccountRequest $request)
    {
        try {
            if ($this->userService->deleteAccount($request->password)) {
                return response()->json(['message' => 'Conta excluída com sucesso.']);
            }
            return response()->json(['message' => 'Senha incorreta.'], 401);
        } catch (Exception $e) {
            return response()->json(['message' => 'Oops! Erro interno no servidor. ' . $e->getMessage()], 500);
        }
    }
}
