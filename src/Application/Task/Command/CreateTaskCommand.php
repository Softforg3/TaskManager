<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

class CreateTaskCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $description,
        public readonly string $assignedUserId,
    ) {
    }
}
