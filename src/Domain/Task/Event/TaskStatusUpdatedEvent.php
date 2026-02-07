<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use App\Domain\Shared\Event\DomainEventInterface;
use DateTimeImmutable;

final readonly class TaskStatusUpdatedEvent implements DomainEventInterface
{
    private DateTimeImmutable $occurredAt;

    public function __construct(
        private string $taskId,
        private string $previousStatus,
        private string $newStatus,
    ) {
        $this->occurredAt = new DateTimeImmutable();
    }

    public function getAggregateId(): string
    {
        return $this->taskId;
    }

    public function getAggregateType(): string
    {
        return 'Task';
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

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
