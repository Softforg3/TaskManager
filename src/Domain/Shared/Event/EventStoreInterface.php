<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

interface EventStoreInterface
{
    public function append(DomainEventInterface $event): void;

    /** @return array<int, array<string, mixed>> */
    public function getEventsForAggregate(string $aggregateId): array;
}
