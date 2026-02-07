<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\TaskStatus;

class StatusTransitionResolver
{
    /** @var StatusTransitionStrategyInterface[] */
    private array $strategies;

    public function __construct(
        TodoToInProgressStrategy $todoToInProgress,
        InProgressToDoneStrategy $inProgressToDone,
    ) {
        $this->strategies = [$todoToInProgress, $inProgressToDone];
    }

    public function canTransition(TaskStatus $from, TaskStatus $to): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($from, $to)) {
                return true;
            }
        }

        return false;
    }

    public function assertTransition(TaskStatus $from, TaskStatus $to): void
    {
        if (!$this->canTransition($from, $to)) {
            throw new \DomainException(sprintf(
                'Invalid status transition from %s to %s',
                $from->value,
                $to->value,
            ));
        }
    }
}
