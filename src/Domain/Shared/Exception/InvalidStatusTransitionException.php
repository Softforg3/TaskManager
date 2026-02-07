<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidStatusTransitionException extends DomainException
{
    public static function fromTo(string $from, string $to): self
    {
        return new self(sprintf(
            'Cannot transition task status from "%s" to "%s".',
            $from,
            $to,
        ));
    }
}
