<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Web;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\Task\Command\CreateTask\CreateTaskCommand;
use App\Application\Task\Command\UpdateTaskStatus\UpdateTaskStatusCommand;
use App\Application\Task\Query\GetTasks\GetTasksQuery;
use App\Application\User\Dto\UserCollection;
use App\Application\User\Query\GetAllUsers\GetAllUsersQuery;
use App\Domain\Shared\Exception\DomainException;
use App\Domain\User\User;
use App\Infrastructure\Security\TaskVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskWebController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/panel/tasks', name: 'web_tasks', methods: ['GET'])]
    public function list(Request $request): Response
    {
        /** @var UserCollection $allUsers */
        $allUsers = $this->queryBus->ask(new GetAllUsersQuery());
        $users = $allUsers->keyById();

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $isAdmin = $this->isGranted(TaskVoter::ASSIGN_TO_OTHERS);

        $filters = array_filter([
            'title' => $request->query->getString('title'),
            'description' => $request->query->getString('description'),
            'assignedUserId' => $request->query->getString('assignedUserId'),
        ]);

        if (!$isAdmin) {
            $filters['assignedUserId'] = $currentUser->getId()->toString();
        }

        $tasks = $this->queryBus->ask(new GetTasksQuery(
            assignedUserId: $filters['assignedUserId'] ?? null,
            title: $filters['title'] ?? null,
            description: $filters['description'] ?? null,
        ));

        return $this->render('tasks/list.html.twig', [
            'tasks' => $tasks,
            'users' => $users,
            'filters' => $filters,
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/panel/tasks/create', name: 'web_tasks_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $assignedUserId = $this->isGranted(TaskVoter::ASSIGN_TO_OTHERS)
            ? $request->request->getString('assignedUserId')
            : $currentUser->getId()->toString();

        try {
            $this->commandBus->handle(new CreateTaskCommand(
                title: $request->request->getString('title'),
                description: $request->request->getString('description'),
                assignedUserId: $assignedUserId,
            ));

            $this->addFlash('success', 'Task created successfully.');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('web_tasks');
    }

    #[Route('/panel/tasks/{id}/status', name: 'web_tasks_update_status', methods: ['POST'], requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'])]
    public function updateStatus(string $id, Request $request): Response
    {
        try {
            $this->commandBus->handle(new UpdateTaskStatusCommand(
                taskId: $id,
                newStatus: $request->request->getString('status'),
            ));

            $this->addFlash('success', 'Task status updated.');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('web_tasks');
    }
}
