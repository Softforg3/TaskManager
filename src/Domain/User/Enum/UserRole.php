<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum UserRole: string
{
    case ADMIN = 'ROLE_ADMIN';
    case MEMBER = 'ROLE_USER';
}
