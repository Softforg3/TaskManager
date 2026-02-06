<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use App\Domain\User\Enum\UserRole;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'integer', unique: true)]
    private int $externalId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $username;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'string', length: 20)]
    private string $role;

    private function __construct()
    {
    }

    public static function create(
        UserId $id,
        int $externalId,
        string $name,
        Email $email,
        string $username,
        string $password,
        UserRole $role
    ): self {
        $user = new self();
        $user->id = $id->toString();
        $user->externalId = $externalId;
        $user->name = $name;
        $user->email = $email->toString();
        $user->username = $username;
        $user->password = $password;
        $user->role = $role->value;

        return $user;
    }

    public function getId(): UserId
    {
        return UserId::fromString($this->id);
    }

    public function getExternalId(): int
    {
        return $this->externalId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return Email::fromString($this->email);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRole(): UserRole
    {
        return UserRole::from($this->role);
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
    }

    public function updateFromApiData(string $name, Email $email): void
    {
        $this->name = $name;
        $this->email = $email->toString();
    }
}
