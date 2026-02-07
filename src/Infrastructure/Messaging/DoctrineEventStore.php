<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use App\Domain\Task\Event\DomainEventInterface;
use Doctrine\DBAL\Connection;

class DoctrineEventStore
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function append(DomainEventInterface $event): void
    {
        $this->connection->insert('event_store', [
            'aggregate_id' => $event->getAggregateId(),
            'aggregate_type' => 'Task',
            'event_type' => $event->getEventType(),
            'payload' => json_encode($event->getPayload(), JSON_THROW_ON_ERROR),
            'occurred_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    public function getEventsForAggregate(string $aggregateId): array
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM event_store WHERE aggregate_id = :id ORDER BY occurred_at ASC, id ASC',
            ['id' => $aggregateId],
        );

        return array_map(function (array $row): array {
            $row['payload'] = json_decode($row['payload'], true, 512, JSON_THROW_ON_ERROR);
            return $row;
        }, $result);
    }
}
