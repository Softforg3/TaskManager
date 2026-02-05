<?php

declare(strict_types=1);

namespace App\Application\User\Factory;

use App\Domain\User\Factory\UserFactoryInterface;
use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory implements UserFactoryInterface
{
    private const DEFAULT_PASSWORD = 'Program@2026';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createFromApiData(array $apiData): User
    {
        $this->validateApiData($apiData);

        $role = ($apiData['id'] === 1) ? UserRole::ADMIN : UserRole::MEMBER;

        $user = User::create(
            UserId::generate(),
            $apiData['id'],
            $apiData['name'],
            $apiData['email'],
            $apiData['username'],
            '', // temporary, will be hashed below
            $role,
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, self::DEFAULT_PASSWORD);
        $user->setPassword($hashedPassword);

        return $user;
    }

    private function validateApiData(array $apiData): void
    {
        $required = ['id', 'name', 'email', 'username'];

        foreach ($required as $field) {
            if (!isset($apiData[$field]) || (is_string($apiData[$field]) && trim($apiData[$field]) === '')) {
                throw new \InvalidArgumentException(sprintf('Missing required field: %s', $field));
            }
        }
    }
}
