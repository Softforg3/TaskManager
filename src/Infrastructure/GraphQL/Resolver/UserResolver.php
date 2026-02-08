<?php

declare(strict_types=1);

namespace App\Infrastructure\GraphQL\Resolver;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\Shared\Bus\QueryBusInterface;
use App\Application\User\Command\SyncUsers\SyncUsersCommand;
use App\Application\User\Dto\UserDto;
use App\Application\User\Query\GetAllUsers\GetAllUsersQuery;
use App\Domain\User\User;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class UserResolver
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private Security $security,
    ) {
    }

    public function me(): ?array
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null;
        }

        return UserDto::fromDomain($user)->toArray();
    }

    /** @return array<int, array<string, mixed>> */
    public function users(): array
    {
        $users = $this->queryBus->ask(new GetAllUsersQuery());

        return $users->toArray();
    }

    public function syncUsers(): bool
    {
        $this->commandBus->handle(new SyncUsersCommand());

        return true;
    }
}
