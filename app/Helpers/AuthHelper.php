<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    public static function user(): ?User
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user;
    }

    public static function hasPermission(string $permission): bool
    {
        return self::user()?->hasPermission($permission) ?? false;
    }
}
