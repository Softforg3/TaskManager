<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Task\Task;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\User\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $em,
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
        return $this->em->getRepository(Task::class)->findBy(
            ['assignedUserId' => $userId->toString()],
            ['createdAt' => 'DESC'],
        );
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Task::class)->findBy([], ['createdAt' => 'DESC']);
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't');

        if (!empty($filters['title'])) {
            $qb->andWhere('t.title LIKE :title')
                ->setParameter('title', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['description'])) {
            $qb->andWhere('t.description LIKE :description')
                ->setParameter('description', '%' . $filters['description'] . '%');
        }

        if (!empty($filters['assignedUserId'])) {
            $qb->andWhere('t.assignedUserId = :assignedUserId')
                ->setParameter('assignedUserId', $filters['assignedUserId']);
        }

        $qb->orderBy('t.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
