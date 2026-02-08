<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Task\Command\CreateTask\CreateTaskCommand;
use App\Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Application\Task\Dto\TaskDto;
use App\Application\Task\Dto\TaskEventDto;
use App\Application\Task\Query\GetTaskById\GetTaskByIdQuery;
use App\Application\Task\Query\GetTaskHistory\GetTaskHistoryQuery;
use App\Application\Task\Query\GetTasks\GetTasksQuery;
use App\Domain\User\User;
use App\Infrastructure\Security\TaskVoter;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class TaskResolver
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private Security $security,
    ) {
    }

    /** @return array<int, array<string, mixed>> */
    public function allTasks(): array
    {
        $tasks = $this->queryBus->ask(new GetTasksQuery());

        return $tasks->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function tasksByUser(string $userId): array
    {
        $tasks = $this->queryBus->ask(new GetTasksQuery(assignedUserId: $userId));

        return $tasks->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function taskHistory(string $taskId): array
    {
        $events = $this->queryBus->ask(new GetTaskHistoryQuery($taskId));

        return array_map(fn (TaskEventDto $event) => [
            'id' => $event->id,
            'aggregateId' => $event->aggregateId,
            'eventType' => $event->eventType,
            'payload' => json_encode($event->payload),
            'occurredAt' => $event->occurredAt,
        ], iterator_to_array($events));
    }

    /** @return array<string, mixed> */
    public function createTask(string $title, string $description, string $assignedUserId): array
    {
        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if (!$this->security->isGranted(TaskVoter::ASSIGN_TO_OTHERS)) {
            $assignedUserId = $currentUser->getId()->toString();
        }

        $command = new CreateTaskCommand($title, $description, $assignedUserId);
        $this->commandBus->handle($command);

        /** @var TaskDto $task */
        $task = $this->queryBus->ask(new GetTaskByIdQuery($command->id));

        return $task->toArray();
    }

    /** @return array<string, mixed> */
    public function updateTaskStatus(string $taskId, string $newStatus): array
    {
        $this->commandBus->handle(new UpdateTaskStatusCommand($taskId, $newStatus));

        /** @var TaskDto $task */
        $task = $this->queryBus->ask(new GetTaskByIdQuery($taskId));

        return $task->toArray();
    }
}
