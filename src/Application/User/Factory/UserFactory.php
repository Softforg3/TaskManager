<?php

declare(strict_types=1);

namespace App\Application\User\Factory;

use App\Domain\User\Factory\UserFactoryInterface;
use App\Domain\User\User;
use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Domain\Shared\Exception\ValidationException;
use App\Domain\User\Enum\UserRole;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class UserFactory implements UserFactoryInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private string $defaultPassword,
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
            Email::fromString($apiData['email']),
            $apiData['username'],
            '', // temporary, will be hashed below
            $role,
        );

        $hashedPassword = $this->passwordHasher->hashPassword($user, $this->defaultPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }

    private function validateApiData(array $apiData): void
    {
        $required = ['id', 'name', 'email', 'username'];

        foreach ($required as $field) {
            if (!isset($apiData[$field]) || (is_string($apiData[$field]) && trim($apiData[$field]) === '')) {
                throw ValidationException::emptyField($field);
            }
        }
    }
}
