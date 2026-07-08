<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class ContextMembershipModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function create(string $contextId, array $payload): array
    {
        return $this->client->post("/v1/contexts/{$contextId}/memberships", $payload);
    }

    public function listByContext(string $contextId): array
    {
        return $this->client->get("/v1/contexts/{$contextId}/memberships");
    }

    public function listByIdentity(string $identityId): array
    {
        return $this->client->get("/v1/identities/{$identityId}/memberships");
    }

    /** status='revoked' é a saída de contexto (definitiva, sem carência, task #834). */
    public function updateStatus(string $membershipId, string $status): array
    {
        return $this->client->patch("/v1/memberships/{$membershipId}/status", ['status' => $status]);
    }

    public function addRoles(string $membershipId, array $roleIds): array
    {
        return $this->client->post("/v1/memberships/{$membershipId}/roles", ['roleIds' => $roleIds]);
    }

    public function removeRoles(string $membershipId, array $roleIds): array
    {
        return $this->client->post("/v1/memberships/{$membershipId}/roles/remove", ['roleIds' => $roleIds]);
    }
}
