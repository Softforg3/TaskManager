<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Task\Strategy;

use App\Domain\Task\Strategy\InProgressToDoneStrategy;
use App\Domain\Task\Strategy\StatusTransitionResolver;
use App\Domain\Task\Strategy\TodoToInProgressStrategy;
use App\Domain\Task\Enum\TaskStatus;
use PHPUnit\Framework\TestCase;

class StatusTransitionTest extends TestCase
{
    private StatusTransitionResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new StatusTransitionResolver([
            new TodoToInProgressStrategy(),
            new InProgressToDoneStrategy(),
        ]);
    }

    public function testResolveReturnsStrategyForTodoToInProgress(): void
    {
        $strategy = $this->resolver->resolve(TaskStatus::TODO, TaskStatus::IN_PROGRESS);

        $this->assertInstanceOf(TodoToInProgressStrategy::class, $strategy);
    }

    public function testResolveReturnsStrategyForInProgressToDone(): void
    {
        $strategy = $this->resolver->resolve(TaskStatus::IN_PROGRESS, TaskStatus::DONE);

        $this->assertInstanceOf(InProgressToDoneStrategy::class, $strategy);
    }

    public function testResolveThrowsOnTodoToDone(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\InvalidStatusTransitionException::class);
        $this->expectExceptionMessage('Cannot transition task status from "TODO" to "DONE".');

        $this->resolver->resolve(TaskStatus::TODO, TaskStatus::DONE);
    }

    public function testResolveThrowsOnDoneToTodo(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\InvalidStatusTransitionException::class);

        $this->resolver->resolve(TaskStatus::DONE, TaskStatus::TODO);
    }

    public function testResolveThrowsOnDoneToInProgress(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\InvalidStatusTransitionException::class);

        $this->resolver->resolve(TaskStatus::DONE, TaskStatus::IN_PROGRESS);
    }

    public function testResolveThrowsOnInProgressToTodo(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\InvalidStatusTransitionException::class);

        $this->resolver->resolve(TaskStatus::IN_PROGRESS, TaskStatus::TODO);
    }

    public function testResolveThrowsOnSameStatus(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\InvalidStatusTransitionException::class);

        $this->resolver->resolve(TaskStatus::TODO, TaskStatus::TODO);
    }
}
