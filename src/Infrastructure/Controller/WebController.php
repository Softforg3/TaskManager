<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\User\Command\SyncUsersCommand;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class WebController extends AbstractController
{
    #[Route('/', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('web_users');
        }

        return $this->render('login.html.twig', [
            'last_username' => $authUtils->getLastUsername(),
            'error' => $authUtils->getLastAuthenticationError()?->getMessageKey(),
        ]);
    }

    #[Route('/panel/users', name: 'web_users', methods: ['GET'])]
    public function users(UserRepositoryInterface $userRepository): Response
    {
        return $this->render('users/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/panel/users/sync', name: 'web_users_sync', methods: ['POST'])]
    public function syncUsers(MessageBusInterface $messageBus): Response
    {
        $envelope = $messageBus->dispatch(new SyncUsersCommand());
        $handledStamp = $envelope->last(\Symfony\Component\Messenger\Stamp\HandledStamp::class);
        $count = $handledStamp?->getResult() ?? 0;

        $this->addFlash('success', sprintf('Synced %d users from JSONPlaceholder API.', $count));

        return $this->redirectToRoute('web_users');
    }
}
