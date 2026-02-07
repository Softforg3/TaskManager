<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\User\UserId;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function save(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function findById(TaskId $id): ?Task
    {
        return $this->em->find(Task::class, $id->toString());
    }

    public function findByUserId(UserId $userId): array
    {
        return $this->em->getRepository(Task::class)->findBy([
            'assignedUserId' => $userId->toString(),
        ]);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Task::class)->findAll();
    }
}
