<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\User\Command\SyncUsersCommand;
use App\Domain\User\User;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\MessageBusInterface;

class UserResolver
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly Security $security,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function me(): ?array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return $this->toArray($user);
    }

    /** @return array<int, array<string, mixed>> */
    public function users(): array
    {
        $users = $this->userRepository->findAll();

        return array_map(fn (User $user) => $this->toArray($user), $users);
    }

    public function syncUsers(): bool
    {
        $this->messageBus->dispatch(new SyncUsersCommand());

        return true;
    }

    /** @return array<string, mixed> */
    private function toArray(User $user): array
    {
        return [
            'id' => $user->getId()->toString(),
            'externalId' => $user->getExternalId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'role' => $user->getRole()->value,
        ];
    }
}
