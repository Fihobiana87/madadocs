<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function findByEmail(string $email): ?array
    {
        return self::first('email', mb_strtolower($email));
    }

    public static function register(string $name, string $email, string $password): int
    {
        return self::create([
            'name' => $name,
            'email' => mb_strtolower($email),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function attempt(string $email, string $password): ?array
    {
        $user = self::findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }
}
