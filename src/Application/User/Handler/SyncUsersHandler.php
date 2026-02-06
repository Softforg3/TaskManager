<?php

declare(strict_types=1);

namespace App\Application\User\Handler;

use App\Application\User\Command\SyncUsersCommand;
use App\Domain\User\Factory\UserFactoryInterface;
use App\Domain\User\UserRepositoryInterface;
use App\Infrastructure\Api\JsonPlaceholderClient;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncUsersHandler
{
    public function __construct(
        private readonly JsonPlaceholderClient $apiClient,
        private readonly UserFactoryInterface $userFactory,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(SyncUsersCommand $command): int
    {
        $apiUsers = $this->apiClient->fetchUsers();
        $synced = 0;

        foreach ($apiUsers as $apiData) {
            $existing = $this->userRepository->findByExternalId($apiData['id']);

            if ($existing !== null) {
                $existing->updateFromApiData($apiData['name'], $apiData['email']);
                $this->userRepository->save($existing);
            } else {
                $user = $this->userFactory->createFromApiData($apiData);
                $this->userRepository->save($user);
            }

            $synced++;
        }

        return $synced;
    }
}
