<?php

declare(strict_types=1);

namespace App\Application\Task\Dto;

final readonly class TaskEventDto
{
    public function __construct(
        public int $id,
        public string $aggregateId,
        public string $eventType,
        public array $payload,
        public string $occurredAt,
    ) {
    }

    /** @param array<string, mixed> $row */
    public static function fromArray(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            aggregateId: $row['aggregate_id'],
            eventType: $row['event_type'],
            payload: $row['payload'],
            occurredAt: $row['occurred_at'],
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'aggregateId' => $this->aggregateId,
            'eventType' => $this->eventType,
            'payload' => $this->payload,
            'occurredAt' => $this->occurredAt,
        ];
    }
}
