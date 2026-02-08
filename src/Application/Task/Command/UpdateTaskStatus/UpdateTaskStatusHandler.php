<?php

declare(strict_types=1);

namespace App\Application\Task\Command\UpdateTaskStatus;

use App\Application\Shared\Bus\EventBusInterface;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Strategy\StatusTransitionResolver;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Shared\Exception\TaskNotFoundException;
use App\Domain\Task\ValueObject\TaskId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class UpdateTaskStatusHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
        private StatusTransitionResolver $transitionResolver,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateTaskStatusCommand $command): void
    {
        $task = $this->taskRepository->findById(TaskId::fromString($command->taskId));

        if ($task === null) {
            throw TaskNotFoundException::forId($command->taskId);
        }

        $newStatus = TaskStatus::from($command->newStatus);
        $strategy = $this->transitionResolver->resolve($task->getStatus(), $newStatus);
        $strategy->execute($task);

        $this->taskRepository->save($task);

        foreach ($task->pullDomainEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
