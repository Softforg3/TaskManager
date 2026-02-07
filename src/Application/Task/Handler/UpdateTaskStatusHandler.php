<?php

declare(strict_types=1);

namespace App\Application\Task\Handler;

use App\Application\Task\Command\UpdateTaskStatusCommand;
use App\Domain\Task\Event\TaskStatusUpdatedEvent;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\Strategy\StatusTransitionResolver;
use App\Infrastructure\Messaging\DoctrineEventStore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateTaskStatusHandler
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly StatusTransitionResolver $transitionResolver,
        private readonly DoctrineEventStore $eventStore,
    ) {
    }

    public function __invoke(UpdateTaskStatusCommand $command): Task
    {
        $task = $this->taskRepository->findById(TaskId::fromString($command->taskId));

        if ($task === null) {
            throw new \InvalidArgumentException('Task not found');
        }

        $newStatus = TaskStatus::from($command->newStatus);
        $previousStatus = $task->getStatus();

        $this->transitionResolver->assertTransition($previousStatus, $newStatus);

        $task->changeStatus($newStatus);
        $this->taskRepository->save($task);

        $event = new TaskStatusUpdatedEvent(
            $task->getId()->toString(),
            $previousStatus->value,
            $newStatus->value,
        );
        $this->eventStore->append($event);

        return $task;
    }
}
