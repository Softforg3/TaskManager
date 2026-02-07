<?php

declare(strict_types=1);

namespace App\Application\User\Dto;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/** @implements IteratorAggregate<int, UserDto> */
final readonly class UserCollection implements IteratorAggregate, Countable
{
    /** @var UserDto[] */
    private array $items;

    public function __construct(UserDto ...$items)
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
        return array_map(fn (UserDto $user) => $user->toArray(), $this->items);
    }

    public function keyById(): array
    {
        $keyed = [];
        foreach ($this->items as $user) {
            $keyed[$user->id] = $user;
        }

        return $keyed;
    }
}
