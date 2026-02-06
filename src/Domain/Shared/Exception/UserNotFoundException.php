<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class UserNotFoundException extends DomainException
{
    public static function forId(string $userId): self
    {
        return new self(sprintf('User with id "%s" was not found.', $userId));
    }

    public static function forUsername(string $username): self
    {
        return new self(sprintf('User with username "%s" was not found.', $username));
    }
}
