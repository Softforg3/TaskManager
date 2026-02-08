<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTaskById;

final readonly class GetTaskByIdQuery
{
    public function __construct(
        public string $taskId,
    ) {
    }
}
