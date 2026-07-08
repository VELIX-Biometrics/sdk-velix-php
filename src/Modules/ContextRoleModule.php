<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class ContextRoleModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function create(array $payload): array
    {
        return $this->client->post('/v1/context-roles', $payload);
    }

    public function list(string $contextType): array
    {
        return $this->client->get('/v1/context-roles', ['contextType' => $contextType]);
    }

    public function linkPermissions(string $roleId, array $permissionIds): array
    {
        return $this->client->post("/v1/context-roles/{$roleId}/permissions", ['permissionIds' => $permissionIds]);
    }
}
