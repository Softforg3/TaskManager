<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTasks;

final readonly class GetTasksQuery
{
    public function __construct(
        public ?string $assignedUserId = null,
        public ?string $title = null,
        public ?string $description = null,
    ) {
    }
}
