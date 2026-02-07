<?php

declare(strict_types=1);

namespace App\Application\Task\Handler;

use App\Application\Task\Command\CreateTaskCommand;
use App\Domain\Task\Event\TaskCreatedEvent;
use App\Domain\Task\Factory\TaskFactoryInterface;
use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserId;
use App\Infrastructure\Messaging\DoctrineEventStore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateTaskHandler
{
    public function __construct(
        private readonly TaskFactoryInterface $taskFactory,
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly DoctrineEventStore $eventStore,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): Task
    {
        $task = $this->taskFactory->create(
            $command->title,
            $command->description,
            UserId::fromString($command->assignedUserId),
        );

        $this->taskRepository->save($task);

        $event = new TaskCreatedEvent(
            $task->getId()->toString(),
            $task->getTitle(),
            $task->getDescription(),
            $task->getStatus()->value,
            $task->getAssignedUserId()->toString(),
        );
        $this->eventStore->append($event);

        return $task;
    }
}
