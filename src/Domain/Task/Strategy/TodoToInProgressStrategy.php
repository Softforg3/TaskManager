<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\TaskStatus;

class TodoToInProgressStrategy implements StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool
    {
        return $from === TaskStatus::TODO && $to === TaskStatus::IN_PROGRESS;
    }
}
