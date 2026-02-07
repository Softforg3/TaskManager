<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Task;

final readonly class InProgressToDoneStrategy implements StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool
    {
        return $from === TaskStatus::IN_PROGRESS && $to === TaskStatus::DONE;
    }

    public function execute(Task $task): void
    {
        $task->changeStatus(TaskStatus::DONE);
    }
}
