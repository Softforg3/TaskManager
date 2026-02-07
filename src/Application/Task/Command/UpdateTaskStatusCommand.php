<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

class UpdateTaskStatusCommand
{
    public function __construct(
        public readonly string $taskId,
        public readonly string $newStatus,
    ) {
    }
}
