<?php
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Exception;
use Illuminate\Auth\Events\PasswordReset;

class AuthService
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);

        $token = $user->createToken($user->name . 'authToken')->plainTextToken;

        return [
            'message' => 'Usuário registrado com sucesso.',
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new Exception('Credenciais inválidas.', 401);
        }

        $token = $user->createToken($user->name . ' - authToken')->plainTextToken;

        return [
            'message' => 'Usuário logado com sucesso.',
            'user' => $user,
            'token' => $token
        ];
    }

    public function sendResetLink(string $email): array
    {
        if (!$this->userRepository->existsByEmail($email)) {
            throw new Exception('O e-mail informado não está cadastrado.', 422);
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return ['message' => 'Link para redefinição de senha enviado por email.'];
        }

        throw new Exception('Não foi possível enviar o link de redefinição.', 422);
    }

    public function resetPassword(array $credentials): array
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $this->userRepository->updatePassword($user, $password);
                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ['message' => 'Senha redefinida com sucesso.'];
        }

        throw new Exception('Oops! Token ou e-mail inválido.', 400);
    }
}
