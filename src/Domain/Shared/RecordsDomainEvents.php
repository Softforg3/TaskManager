<?php

declare(strict_types=1);

namespace App\Domain\Shared;

use App\Domain\Shared\Event\DomainEventInterface;

trait RecordsDomainEvents
{
    /** @var DomainEventInterface[] */
    private array $domainEvents = [];

    protected function recordEvent(DomainEventInterface $event): void
    {
        $this->domainEvents[] = $event;
    }

    /** @return DomainEventInterface[] */
    public function pullDomainEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
