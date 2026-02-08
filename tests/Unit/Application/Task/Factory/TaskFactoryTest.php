<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task\Factory;

use App\Application\Task\Factory\TaskFactory;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\User\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class TaskFactoryTest extends TestCase
{
    private TaskFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new TaskFactory();
    }

    public function testCreateReturnsTaskWithCorrectData(): void
    {
        $taskId = TaskId::generate();
        $userId = UserId::generate();

        $task = $this->factory->create($taskId, 'Test task', 'Some description', $userId);

        $this->assertTrue($task->getId()->equals($taskId));
        $this->assertSame('Test task', $task->getTitle());
        $this->assertSame('Some description', $task->getDescription());
        $this->assertSame(TaskStatus::TODO, $task->getStatus());
        $this->assertTrue($task->getAssignedUserId()->equals($userId));
    }

    public function testCreateWithDifferentIdsPreservesIdentity(): void
    {
        $userId = UserId::generate();

        $task1 = $this->factory->create(TaskId::generate(), 'Task 1', 'Desc', $userId);
        $task2 = $this->factory->create(TaskId::generate(), 'Task 2', 'Desc', $userId);

        $this->assertFalse($task1->getId()->equals($task2->getId()));
    }

    public function testCreateSetsInitialTimestamps(): void
    {
        $task = $this->factory->create(TaskId::generate(), 'Task', 'Desc', UserId::generate());

        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $task->getUpdatedAt());
    }

    public function testCreateWithEmptyTitleThrowsException(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\ValidationException::class);
        $this->expectExceptionMessage('Field "title" cannot be empty.');

        $this->factory->create(TaskId::generate(), '', 'Description', UserId::generate());
    }

    public function testCreateWithWhitespaceTitleThrowsException(): void
    {
        $this->expectException(\App\Domain\Shared\Exception\ValidationException::class);

        $this->factory->create(TaskId::generate(), '   ', 'Description', UserId::generate());
    }

    public function testCreateWithEmptyDescriptionIsAllowed(): void
    {
        $task = $this->factory->create(TaskId::generate(), 'Title', '', UserId::generate());

        $this->assertSame('', $task->getDescription());
    }
}
