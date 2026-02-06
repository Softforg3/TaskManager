<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class ValidationException extends DomainException
{
    public static function emptyField(string $field): self
    {
        return new self(sprintf('Field "%s" cannot be empty.', $field));
    }

    public static function invalidFormat(string $field, string $value): self
    {
        return new self(sprintf('Invalid format for field "%s": %s', $field, $value));
    }
}
