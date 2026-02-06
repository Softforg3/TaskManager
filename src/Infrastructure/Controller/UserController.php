<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\User\Command\SyncUsersCommand;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/users', name: 'api_users_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        return $this->json(array_map(fn (User $user) => [
            'id' => $user->getId()->toString(),
            'externalId' => $user->getExternalId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()->value,
        ], $users));
    }

    #[Route('/users/sync', name: 'api_users_sync', methods: ['POST'])]
    public function sync(): JsonResponse
    {
        $envelope = $this->messageBus->dispatch(new SyncUsersCommand());
        $handledStamp = $envelope->last(HandledStamp::class);
        $synced = $handledStamp?->getResult() ?? 0;

        return $this->json([
            'message' => sprintf('Synced %d users', $synced),
            'count' => $synced,
        ]);
    }
}
