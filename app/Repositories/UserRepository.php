<?php
namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRepository
{
    public function create(array $data): User
    {
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->save();
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function existsByEmail(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }

    public function deleteUserContacts(User $user): void
    {
        $user->contacts()->delete();
    }

    public function checkPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
