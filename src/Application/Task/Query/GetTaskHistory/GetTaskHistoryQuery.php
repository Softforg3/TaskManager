<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTaskHistory;

final readonly class GetTaskHistoryQuery
{
    public function __construct(
        public string $taskId,
    ) {
    }
}
