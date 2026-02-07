<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Shared\RecordsDomainEvents;
use App\Domain\Task\Enum\TaskStatus;
use App\Domain\Task\Event\TaskCreatedEvent;
use App\Domain\Task\Event\TaskStatusUpdatedEvent;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tasks')]
final class Task
{
    use RecordsDomainEvents;
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'string', length: 36)]
    private string $assignedUserId;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    private function __construct()
    {
    }

    public static function create(
        TaskId $id,
        string $title,
        string $description,
        TaskStatus $status,
        UserId $assignedUserId
    ): self {
        $task = new self();
        $task->id = $id->toString();
        $task->title = $title;
        $task->description = $description;
        $task->status = $status->value;
        $task->assignedUserId = $assignedUserId->toString();
        $task->createdAt = new DateTimeImmutable();
        $task->updatedAt = new DateTimeImmutable();

        $task->recordEvent(new TaskCreatedEvent(
            $task->id,
            $task->title,
            $task->description,
            $task->status,
            $task->assignedUserId,
        ));

        return $task;
    }

    public function getId(): TaskId
    {
        return TaskId::fromString($this->id);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): TaskStatus
    {
        return TaskStatus::from($this->status);
    }

    public function getAssignedUserId(): UserId
    {
        return UserId::fromString($this->assignedUserId);
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function changeStatus(TaskStatus $newStatus): void
    {
        $previousStatus = $this->status;
        $this->status = $newStatus->value;
        $this->updatedAt = new DateTimeImmutable();

        $this->recordEvent(new TaskStatusUpdatedEvent(
            $this->id,
            $previousStatus,
            $newStatus->value,
        ));
    }
}
