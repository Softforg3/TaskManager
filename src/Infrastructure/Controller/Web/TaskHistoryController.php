<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Web;

use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Task\Query\GetTaskHistory\GetTaskHistoryQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TaskHistoryController extends AbstractController
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/panel/tasks/{id}/history', name: 'web_tasks_history', methods: ['GET'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function history(string $id): JsonResponse
    {
        $events = $this->queryBus->ask(new GetTaskHistoryQuery($id));

        return $this->json($events->toArray());
    }
}
