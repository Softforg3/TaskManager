<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Task\Command\CreateTaskCommand;
use App\Application\Task\Command\UpdateTaskStatusCommand;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserId;
use App\Infrastructure\Messaging\DoctrineEventStore;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class TaskResolver
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly DoctrineEventStore $eventStore,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /** @return array<int, array<string, mixed>> */
    public function tasks(?string $userId = null): array
    {
        if ($userId !== null) {
            $tasks = $this->taskRepository->findByUserId(UserId::fromString($userId));
        } else {
            $tasks = $this->taskRepository->findAll();
        }

        return array_map(fn (Task $task) => $this->toArray($task), $tasks);
    }

    /** @return array<int, array<string, mixed>> */
    public function allTasks(): array
    {
        return array_map(
            fn (Task $task) => $this->toArray($task),
            $this->taskRepository->findAll(),
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function taskHistory(string $taskId): array
    {
        $events = $this->eventStore->getEventsForAggregate($taskId);

        return array_map(fn (array $event) => [
            'id' => $event['id'],
            'aggregateId' => $event['aggregate_id'],
            'eventType' => $event['event_type'],
            'payload' => json_encode($event['payload']),
            'occurredAt' => $event['occurred_at'],
        ], $events);
    }

    /** @return array<string, mixed> */
    public function createTask(string $title, string $description, string $assignedUserId): array
    {
        $envelope = $this->messageBus->dispatch(
            new CreateTaskCommand($title, $description, $assignedUserId),
        );

        $handledStamp = $envelope->last(HandledStamp::class);
        $task = $handledStamp->getResult();

        return $this->toArray($task);
    }

    /** @return array<string, mixed> */
    public function updateTaskStatus(string $taskId, string $newStatus): array
    {
        $envelope = $this->messageBus->dispatch(
            new UpdateTaskStatusCommand($taskId, $newStatus),
        );

        $handledStamp = $envelope->last(HandledStamp::class);
        $task = $handledStamp->getResult();

        return $this->toArray($task);
    }

    /** @return array<string, mixed> */
    private function toArray(Task $task): array
    {
        return [
            'id' => $task->getId()->toString(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus()->value,
            'assignedUserId' => $task->getAssignedUserId()->toString(),
            'createdAt' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
