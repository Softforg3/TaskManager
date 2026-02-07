<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Task;

final readonly class TodoToInProgressStrategy implements StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool
    {
        return $from === TaskStatus::TODO && $to === TaskStatus::IN_PROGRESS;
    }

    public function execute(Task $task): void
    {
        $task->changeStatus(TaskStatus::IN_PROGRESS);
    }
}
