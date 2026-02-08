<?php

declare(strict_types=1);

namespace App\Application\Task\Command\CreateTask;

use Symfony\Component\Uid\Uuid;

final readonly class CreateTaskCommand
{
    public string $id;

    public function __construct(
        public string $title,
        public string $description,
        public string $assignedUserId,
    ) {
        $this->id = Uuid::v4()->toRfc4122();
    }
}
