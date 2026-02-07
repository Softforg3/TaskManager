<?php

declare(strict_types=1);

namespace App\Application\User\Query\GetAllUsers;

use App\Application\User\Dto\UserCollection;
use App\Application\User\Dto\UserDto;
use App\Domain\User\UserRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class GetAllUsersHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(GetAllUsersQuery $query): UserCollection
    {
        $users = $this->userRepository->findAll();

        return new UserCollection(...array_map(fn ($user) => UserDto::fromDomain($user), $users));
    }
}
