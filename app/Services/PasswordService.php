<?php
namespace App\Services;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;

class PasswordService
{
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function verifyPassword(string $password, string $hashedPassword): bool
    {
        return Hash::check($password, $hashedPassword);
    }

    // public function forgetPassword(string mail, string $newPassword): void
    // {
    //     $user = User
    // }
}