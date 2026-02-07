<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use App\Domain\Shared\Event\DomainEventInterface;
use DateTimeImmutable;

final readonly class TaskCreatedEvent implements DomainEventInterface
{
    private DateTimeImmutable $occurredAt;

    public function __construct(
        private string $taskId,
        private string $title,
        private string $description,
        private string $status,
        private string $assignedUserId,
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

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
