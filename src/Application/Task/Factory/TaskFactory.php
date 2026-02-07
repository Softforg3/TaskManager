<?php

declare(strict_types=1);

namespace App\Application\Task\Factory;

use App\Domain\Task\Factory\TaskFactoryInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskStatus;
use App\Domain\User\UserId;

class TaskFactory implements TaskFactoryInterface
{
    public function create(string $title, string $description, UserId $assignedUserId): Task
    {
        if (trim($title) === '') {
            throw new \InvalidArgumentException('Task title cannot be empty');
        }

        return Task::create(
            TaskId::generate(),
            $title,
            $description,
            TaskStatus::TODO,
            $assignedUserId,
        );
    }
}
