<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

final class TaskCreatedEvent implements DomainEventInterface
{
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        private readonly string $taskId,
        private readonly string $title,
        private readonly string $description,
        private readonly string $status,
        private readonly string $assignedUserId,
    ) {
        $this->occurredAt = new \DateTimeImmutable();
    }

    public function getAggregateId(): string
    {
        return $this->taskId;
    }

    public function getEventType(): string
    {
        return 'TaskCreatedEvent';
    }

    public function getPayload(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'assignedUserId' => $this->assignedUserId,
        ];
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
