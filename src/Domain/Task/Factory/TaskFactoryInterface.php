<?php

declare(strict_types=1);

namespace App\Domain\Task\Factory;

use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;

interface TaskFactoryInterface
{
    public function create(TaskId $id, string $title, string $description, UserId $assignedUserId): Task;
}
