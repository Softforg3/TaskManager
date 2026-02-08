<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTaskById;

use App\Application\Task\Dto\TaskDto;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Shared\Exception\TaskNotFoundException;
use App\Domain\Task\ValueObject\TaskId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTaskByIdHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(GetTaskByIdQuery $query): TaskDto
    {
        $task = $this->taskRepository->findById(TaskId::fromString($query->taskId));

        if ($task === null) {
            throw TaskNotFoundException::forId($query->taskId);
        }

        return TaskDto::fromDomain($task);
    }
}
