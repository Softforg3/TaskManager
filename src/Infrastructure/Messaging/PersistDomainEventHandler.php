<?php

declare(strict_types=1);

namespace App\Infrastructure\Messaging;

use App\Domain\Shared\Event\DomainEventInterface;
use App\Domain\Shared\Event\EventStoreInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class PersistDomainEventHandler
{
    public function __construct(
        private EventStoreInterface $eventStore,
    ) {
    }

    public function __invoke(DomainEventInterface $event): void
    {
        $this->eventStore->append($event);
    }
}
