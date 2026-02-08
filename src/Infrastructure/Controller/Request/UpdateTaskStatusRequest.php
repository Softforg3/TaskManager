<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateTaskStatusRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Choice(choices: ['IN_PROGRESS', 'DONE'])]
        public string $status,
    ) {
    }
}
