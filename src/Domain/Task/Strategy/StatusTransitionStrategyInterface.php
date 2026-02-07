<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\TaskStatus;

interface StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool;
}
