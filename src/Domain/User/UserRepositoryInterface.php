<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function findById(UserId $id): ?User;

    public function findByUsername(string $username): ?User;

    public function findByExternalId(int $externalId): ?User;

    /** @return User[] */
    public function findAll(): array;
}
