<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class ContextPermissionModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function create(array $payload): array
    {
        return $this->client->post('/v1/context-permissions', $payload);
    }

    public function list(?string $category = null): array
    {
        return $this->client->get('/v1/context-permissions', $category !== null ? ['category' => $category] : []);
    }
}
