<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTaskRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $title,
        #[Assert\NotBlank]
        public string $description,
        #[Assert\Uuid]
        public ?string $assignedUserId = null,
    ) {
    }
}
