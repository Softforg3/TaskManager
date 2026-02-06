<?php

declare(strict_types=1);

namespace App\Application\User\Command\SyncUsers;

use App\Domain\User\Factory\UserFactoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Api\JsonPlaceholderClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class SyncUsersHandler
{
    public function __construct(
        private JsonPlaceholderClient $apiClient,
        private UserFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(SyncUsersCommand $command): void
    {
        $apiUsers = $this->apiClient->fetchUsers();

        foreach ($apiUsers as $apiData) {
            $existing = $this->userRepository->findByExternalId($apiData['id']);

            if ($existing !== null) {
                $existing->updateFromApiData($apiData['name'], Email::fromString($apiData['email']));
                $this->userRepository->save($existing);
            } else {
                $user = $this->userFactory->createFromApiData($apiData);
                $this->userRepository->save($user);
            }
        }
    }
}
