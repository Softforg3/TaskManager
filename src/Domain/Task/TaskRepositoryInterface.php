<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;

    public function findById(TaskId $id): ?Task;

    /** @return Task[] */
    public function findByUserId(UserId $userId): array;

    /** @return Task[] */
    public function findAll(): array;

    /**
     * @param array{title?: string, description?: string, assignedUserId?: string} $filters
     * @return Task[]
     */
    public function findByFilters(array $filters): array;
}
