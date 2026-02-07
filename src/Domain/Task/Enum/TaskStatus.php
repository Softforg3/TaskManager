<?php

declare(strict_types=1);

namespace App\Domain\Task\Enum;

enum TaskStatus: string
{
    case TODO = 'TODO';
    case IN_PROGRESS = 'IN_PROGRESS';
    case DONE = 'DONE';
}
