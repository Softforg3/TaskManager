<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Task\Command\CreateTaskCommand;
use App\Application\Task\Command\UpdateTaskStatusCommand;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserId;
use App\Infrastructure\Messaging\DoctrineEventStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository,
        private readonly DoctrineEventStore $eventStore,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route('', name: 'api_tasks_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $userId = $request->query->get('userId');

        if ($userId !== null) {
            $tasks = $this->taskRepository->findByUserId(UserId::fromString($userId));
        } else {
            $tasks = $this->taskRepository->findAll();
        }

        return $this->json(array_map(fn (Task $task) => $this->toArray($task), $tasks));
    }

    #[Route('', name: 'api_tasks_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $envelope = $this->messageBus->dispatch(new CreateTaskCommand(
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['assignedUserId'] ?? '',
        ));

        $handledStamp = $envelope->last(HandledStamp::class);
        $task = $handledStamp->getResult();

        return $this->json($this->toArray($task), 201);
    }

    #[Route('/{id}/status', name: 'api_tasks_update_status', methods: ['PATCH'])]
    public function updateStatus(string $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $envelope = $this->messageBus->dispatch(new UpdateTaskStatusCommand(
            $id,
            $data['status'] ?? '',
        ));

        $handledStamp = $envelope->last(HandledStamp::class);
        $task = $handledStamp->getResult();

        return $this->json($this->toArray($task));
    }

    #[Route('/{id}/history', name: 'api_tasks_history', methods: ['GET'])]
    public function history(string $id): JsonResponse
    {
        $events = $this->eventStore->getEventsForAggregate($id);

        return $this->json(array_map(fn (array $event) => [
            'id' => $event['id'],
            'aggregateId' => $event['aggregate_id'],
            'eventType' => $event['event_type'],
            'payload' => $event['payload'],
            'occurredAt' => $event['occurred_at'],
        ], $events));
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
