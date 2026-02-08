<?php

declare(strict_types=1);

namespace App\Application\Task\Dto;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, TaskEventDto> */
final readonly class TaskEventCollection implements IteratorAggregate, Countable
{
    /** @var TaskEventDto[] */
    private array $items;

    public function __construct(TaskEventDto ...$items)
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

    /** @return array<int, array<string, mixed>> */
    public function toArray(): array
    {
        return array_map(fn (TaskEventDto $event) => $event->toArray(), $this->items);
    }
}
