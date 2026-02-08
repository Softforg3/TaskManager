<?php

declare(strict_types=1);

namespace App\Application\Task\Command\CreateTask;

use App\Application\Shared\Bus\EventBusInterface;
use App\Domain\Task\Factory\TaskFactoryInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateTaskHandler
{
    public function __construct(
        private TaskFactoryInterface $taskFactory,
        private TaskRepositoryInterface $taskRepository,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateTaskCommand $command): void
    {
        $task = $this->taskFactory->create(
            TaskId::fromString($command->id),
            $command->title,
            $command->description,
            UserId::fromString($command->assignedUserId),
        );

        $this->taskRepository->save($task);

        foreach ($task->pullDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
