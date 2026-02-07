<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Task;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.status_transition_strategy')]
interface StatusTransitionStrategyInterface
{
    public function supports(TaskStatus $from, TaskStatus $to): bool;

    public function execute(Task $task): void;
}
