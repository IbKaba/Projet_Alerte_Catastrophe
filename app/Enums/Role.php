<?php

namespace App\Enums;

enum Role: string
{
    case CITIZEN = 'citizen';
    case MODERATOR = 'moderator';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::CITIZEN => 'Citoyen',
            self::MODERATOR => 'Modérateur / Secours',
            self::ADMIN => 'Administrateur',
        };
    }

    public function canModerate(): bool
    {
        return in_array($this, [self::MODERATOR, self::ADMIN], true);
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
