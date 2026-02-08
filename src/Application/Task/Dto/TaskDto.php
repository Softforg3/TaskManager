<?php

declare(strict_types=1);

namespace App\Application\Task\Dto;

use App\Domain\Task\Task;

final readonly class TaskDto
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public string $status,
        public string $assignedUserId,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromDomain(Task $task): self
    {
        return new self(
            id: $task->getId()->toString(),
            title: $task->getTitle(),
            description: $task->getDescription(),
            status: $task->getStatus()->value,
            assignedUserId: $task->getAssignedUserId()->toString(),
            createdAt: $task->getCreatedAt()->format('Y-m-d H:i:s'),
            updatedAt: $task->getUpdatedAt()->format('Y-m-d H:i:s'),
        );
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'assignedUserId' => $this->assignedUserId,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
