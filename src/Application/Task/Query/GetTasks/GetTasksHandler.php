<?php

declare(strict_types=1);

namespace App\Application\Task\Query\GetTasks;

use App\Application\Task\Dto\TaskCollection;
use App\Application\Task\Dto\TaskDto;
use App\Domain\Task\TaskRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetTasksHandler
{
    public function __construct(
        private TaskRepositoryInterface $taskRepository,
    ) {
    }

    public function __invoke(GetTasksQuery $query): TaskCollection
    {
        $filters = array_filter([
            'assignedUserId' => $query->assignedUserId,
            'title' => $query->title,
            'description' => $query->description,
        ]);

        $tasks = $filters
            ? $this->taskRepository->findByFilters($filters)
            : $this->taskRepository->findAll();

        return new TaskCollection(...array_map(fn ($task) => TaskDto::fromDomain($task), $tasks));
    }
}
