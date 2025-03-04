<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function deleteAccount(string $password): bool
    {
        try {
            $user = Auth::user();

            if (!$this->userRepository->checkPassword($user, $password)) {
                return false;
            }
            DB::beginTransaction();
            $this->userRepository->deleteUserContacts($user);
            $this->userRepository->deleteUser($user);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao tentar excluir conta de usuário: ' . $e->getMessage());
            throw new Exception('Falha ao excluir conta de usuário.' . $e->getMessage());
        }
    }
}
