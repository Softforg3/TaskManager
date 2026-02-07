<?php

declare(strict_types=1);

namespace App\Domain\Task\Strategy;

use App\Domain\Shared\Exception\InvalidStatusTransitionException;
use App\Domain\Task\Enum\TaskStatus;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class StatusTransitionResolver
{
    /** @param iterable<StatusTransitionStrategyInterface> $strategies */
    public function __construct(
        #[TaggedIterator('app.status_transition_strategy')]
        private iterable $strategies,
    ) {
    }

    public function resolve(TaskStatus $from, TaskStatus $to): StatusTransitionStrategyInterface
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($from, $to)) {
                return $strategy;
            }
        }

        throw InvalidStatusTransitionException::fromTo($from->value, $to->value);
    }
}
