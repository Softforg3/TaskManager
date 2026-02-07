<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\User\UserId;

interface TaskRepositoryInterface
{
    public function save(Task $task): void;

    public function findById(TaskId $id): ?Task;

    /** @return Task[] */
    public function findByUserId(UserId $userId): array;

    /** @return Task[] */
    public function findAll(): array;
}
