<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Task\Command\CreateTask\CreateTaskCommand;
use App\Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Application\Task\Dto\TaskDto;
use App\Application\Task\Query\GetTaskById\GetTaskByIdQuery;
use App\Application\Task\Query\GetTaskHistory\GetTaskHistoryQuery;
use App\Application\Task\Query\GetTasks\GetTasksQuery;
use App\Domain\User\User;
use App\Infrastructure\Controller\Request\CreateTaskRequest;
use App\Infrastructure\Controller\Request\UpdateTaskStatusRequest;
use App\Infrastructure\Security\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
final class TaskController extends AbstractController
{
    private const UUID_PATTERN = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('', name: 'api_tasks_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $tasks = $this->queryBus->ask(new GetTasksQuery(
            assignedUserId: $request->query->get('userId'),
        ));

        return $this->json($tasks->toArray());
    }

    #[Route('', name: 'api_tasks_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateTaskRequest $request): JsonResponse
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $assignedUserId = $this->isGranted(TaskVoter::ASSIGN_TO_OTHERS)
            ? ($request->assignedUserId ?? '')
            : $currentUser->getId()->toString();

        $command = new CreateTaskCommand(
            $request->title,
            $request->description,
            $assignedUserId,
        );

        $this->commandBus->handle($command);

        /** @var TaskDto $task */
        $task = $this->queryBus->ask(new GetTaskByIdQuery($command->id));

        return $this->json($task->toArray(), 201);
    }

    #[Route('/{id}/status', name: 'api_tasks_update_status', methods: ['PATCH'], requirements: ['id' => self::UUID_PATTERN])]
    public function updateStatus(string $id, #[MapRequestPayload] UpdateTaskStatusRequest $request): JsonResponse
    {
        $this->commandBus->handle(new UpdateTaskStatusCommand(
            $id,
            $request->status,
        ));

        /** @var TaskDto $task */
        $task = $this->queryBus->ask(new GetTaskByIdQuery($id));

        return $this->json($task->toArray());
    }

    #[Route('/{id}/history', name: 'api_tasks_history', methods: ['GET'], requirements: ['id' => self::UUID_PATTERN])]
    public function history(string $id): JsonResponse
    {
        $events = $this->queryBus->ask(new GetTaskHistoryQuery($id));

        return $this->json($events->toArray());
    }
}
