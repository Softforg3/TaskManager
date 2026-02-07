<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

final class TaskStatusUpdatedEvent implements DomainEventInterface
{
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        private readonly string $taskId,
        private readonly string $previousStatus,
        private readonly string $newStatus,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getAggregateId(): string
    {
        return $this->taskId;
    }

    public function getEventType(): string
    {
        return 'TaskStatusUpdatedEvent';
    }

    public function getPayload(): array
    {
        return [
            'previousStatus' => $this->previousStatus,
            'newStatus' => $this->newStatus,
        ];
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
