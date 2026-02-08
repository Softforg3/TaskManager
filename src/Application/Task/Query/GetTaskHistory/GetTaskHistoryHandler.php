<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTaskHistory;

use App\Application\Task\Dto\TaskEventCollection;
use App\Application\Task\Dto\TaskEventDto;
use App\Domain\Shared\Event\EventStoreInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTaskHistoryHandler
{
    public function __construct(
        private EventStoreInterface $eventStore,
    ) {
    }

    public function __invoke(GetTaskHistoryQuery $query): TaskEventCollection
    {
        $rows = $this->eventStore->getEventsForAggregate($query->taskId);

        return new TaskEventCollection(...array_map(fn (array $row) => TaskEventDto::fromArray($row), $rows));
    }
}
