<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\User\Command\SyncUsers\SyncUsersCommand;
use App\Application\User\Query\GetAllUsers\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/users', name: 'api_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->queryBus->ask(new GetAllUsersQuery());

        return $this->json($users->toArray());
    }

    #[Route('/users/sync', name: 'api_users_sync', methods: ['POST'])]
    public function sync(): JsonResponse
    {
        $this->commandBus->handle(new SyncUsersCommand());

        return $this->json([
            'message' => 'Users synced successfully',
        ]);
    }
}
