<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

use DateTimeImmutable;

interface DomainEventInterface
{
    public function getAggregateId(): string;

    public function getAggregateType(): string;

    public function getEventType(): string;

    /** @return array<string, mixed> */
    public function getPayload(): array;

    public function getOccurredAt(): DateTimeImmutable;
}
