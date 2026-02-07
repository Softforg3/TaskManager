<?php

declare(strict_types=1);

namespace App\Application\User\Dto;

use App\Domain\User\User;

final readonly class UserDto
{
    public function __construct(
        public string $id,
        public int $externalId,
        public string $name,
        public string $email,
        public string $username,
        public string $role,
    ) {
    }

    public static function fromDomain(User $user): self
    {
        return new self(
            id: $user->getId()->toString(),
            externalId: $user->getExternalId(),
            name: $user->getName(),
            email: $user->getEmail()->toString(),
            username: $user->getUsername(),
            role: $user->getRole()->value,
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'externalId' => $this->externalId,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
        ];
    }
}
