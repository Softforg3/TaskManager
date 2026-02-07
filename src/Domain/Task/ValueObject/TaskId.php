<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

use Symfony\Component\Uid\Uuid;

final readonly class TaskId
{
    private function __construct(
        private Uuid $id
    ) {
    }

    public static function generate(): self
    {
        return new self(Uuid::v4());
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    public function toString(): string
    {
        return $this->id->toRfc4122();
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function equals(self $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
