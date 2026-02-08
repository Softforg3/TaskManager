<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\User\Factory;

use App\Application\User\Factory\UserFactory;
use App\Domain\User\Enum\UserRole;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactoryTest extends TestCase
{
    private UserFactory $factory;

    protected function setUp(): void
    {
        $hasher = $this->createStub(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn('hashed_password');

        $this->factory = new UserFactory($hasher, 'TestPassword123');
    }

    public function testCreateFromApiDataReturnsUserWithCorrectFields(): void
    {
        $user = $this->factory->createFromApiData([
            'id' => 2,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'username' => 'johndoe',
        ]);

        $this->assertSame('John Doe', $user->getName());
        $this->assertSame('john@example.com', $user->getEmail()->toString());
        $this->assertSame('johndoe', $user->getUsername());
    }

    public function testFirstUserGetsAdminRole(): void
    {
        $user = $this->factory->createFromApiData([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'username' => 'admin',
        ]);

        $this->assertSame(UserRole::ADMIN, $user->getRole());
    }

    public function testOtherUsersGetMemberRole(): void
    {
        $user = $this->factory->createFromApiData([
            'id' => 5,
            'name' => 'Regular User',
            'email' => 'regular@example.com',
            'username' => 'regular',
        ]);

        $this->assertSame(UserRole::MEMBER, $user->getRole());
    }

    public function testCreateWithMissingFieldThrowsException(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\ValidationException::class);
        $this->expectExceptionMessage('Field "name" cannot be empty.');

        $this->factory->createFromApiData([
            'id' => 1,
            'email' => 'test@example.com',
            'username' => 'test',
        ]);
    }

    public function testCreateWithEmptyNameThrowsException(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\ValidationException::class);
        $this->expectExceptionMessage('Field "name" cannot be empty.');

        $this->factory->createFromApiData([
            'id' => 1,
            'name' => '',
            'email' => 'test@example.com',
            'username' => 'test',
        ]);
    }

    public function testCreateWithEmptyEmailThrowsException(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\ValidationException::class);
        $this->expectExceptionMessage('Field "email" cannot be empty.');

        $this->factory->createFromApiData([
            'id' => 1,
            'name' => 'Test',
            'email' => '   ',
            'username' => 'test',
        ]);
    }

    public function testCreatedUsersHaveUniqueIds(): void
    {
        $data = ['id' => 3, 'name' => 'User', 'email' => 'u@e.com', 'username' => 'user'];

        $user1 = $this->factory->createFromApiData($data);
        $user2 = $this->factory->createFromApiData($data);

        $this->assertFalse($user1->getId()->equals($user2->getId()));
    }
}
