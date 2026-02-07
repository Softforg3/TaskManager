<?php

declare(strict_types=1);

namespace App\Domain\Task\Factory;

use App\Domain\Task\Task;
use App\Domain\User\UserId;

interface TaskFactoryInterface
{
    public function create(string $title, string $description, UserId $assignedUserId): Task;
}
