<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\TaskStatus;

class InProgressToDoneStrategy implements StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool
    {
        return $from === TaskStatus::IN_PROGRESS && $to === TaskStatus::DONE;
    }
}
