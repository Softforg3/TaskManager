<?php

declare(strict_types=1);

namespace App\Application\Task\Factory;

use App\Domain\Task\Factory\TaskFactoryInterface;
use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\User\ValueObject\UserId;

final readonly class TaskFactory implements TaskFactoryInterface
{
    public function create(TaskId $id, string $title, string $description, UserId $assignedUserId): Task
    {
        if (trim($title) === '') {
            throw ValidationException::emptyField('title');
        }

        return Task::create(
            $id,
            $title,
            $description,
            TaskStatus::TODO,
            $assignedUserId,
        );
    }
}
