<?php

declare(strict_types=1);

namespace App\Application\Task\Dto;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, TaskDto> */
final readonly class TaskCollection implements IteratorAggregate, Countable
{
    /** @var TaskDto[] */
    private array $items;

    public function __construct(TaskDto ...$items)
    {
        $this->items = $items;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return array<int, array<string, string>> */
    public function toArray(): array
    {
        return array_map(fn (TaskDto $task) => $task->toArray(), $this->items);
    }
}
