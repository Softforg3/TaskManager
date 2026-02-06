<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\User\User;

interface UserFactoryInterface
{
    /**
     * @param array{id: int, name: string, email: string, username: string} $apiData
     */
    public function createFromApiData(array $apiData): User;
}
