<?php

declare(strict_types=1);

namespace App\Infrastructure\Api;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final readonly class JsonPlaceholderClient
{
    private const API_URL = 'https://jsonplaceholder.typicode.com/users';

    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    /** @return array<int, array{id: int, name: string, email: string, username: string}> */
    public function fetchUsers(): array
    {
        try {
            $response = $this->httpClient->request('GET', self::API_URL);

            return $response->toArray();
        } catch (Throwable $e) {
            throw new RuntimeException(
                sprintf('Failed to fetch users from JSONPlaceholder: %s', $e->getMessage()),
                0,
                $e
            );
        }
    }
}
