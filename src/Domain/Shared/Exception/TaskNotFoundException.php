<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class TaskNotFoundException extends DomainException
{
    public static function forId(string $taskId): self
    {
        return new self(sprintf('Task with id "%s" was not found.', $taskId));
    }
}
