<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Web;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\User\Command\SyncUsers\SyncUsersCommand;
use App\Application\User\Query\GetAllUsers\GetAllUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserWebController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/panel/users', name: 'web_users', methods: ['GET'])]
    public function list(): Response
    {
        $users = $this->queryBus->ask(new GetAllUsersQuery());

        return $this->render('users/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/panel/users/sync', name: 'web_users_sync', methods: ['POST'])]
    public function sync(): Response
    {
        $this->commandBus->handle(new SyncUsersCommand());

        $this->addFlash('success', 'Users synced from JSONPlaceholder API.');

        return $this->redirectToRoute('web_users');
    }
}
