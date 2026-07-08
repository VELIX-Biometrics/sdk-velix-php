<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

/**
 * /v1/contexts/* — Identity Context (Velix.ID). BearerAuth (JWT de sessão).
 * Ver code/lib/lib-velix-contracts/openapi/public-api.yaml, tag "Identity Context".
 */
class ContextModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function create(array $payload): array
    {
        return $this->client->post('/v1/contexts', $payload);
    }

    public function get(string $id): array
    {
        return $this->client->get("/v1/contexts/{$id}");
    }

    public function list(): array
    {
        return $this->client->get('/v1/contexts');
    }

    public function update(string $id, array $payload): array
    {
        return $this->client->patch("/v1/contexts/{$id}", $payload);
    }

    public function remove(string $id): array
    {
        return $this->client->delete("/v1/contexts/{$id}");
    }

    /** POST /v1/contexts/{contextId}/authorize — Authorization Engine. */
    public function authorize(string $contextId, array $payload): array
    {
        return $this->client->post("/v1/contexts/{$contextId}/authorize", $payload);
    }

    public function listAuthorizationDecisions(string $contextId): array
    {
        return $this->client->get("/v1/contexts/{$contextId}/authorization-decisions");
    }

    /**
     * POST /v1/contexts/{contextId}/link-requests — solicita vínculo cross-tenant.
     * Nunca cria membership diretamente: retorna 202 (PENDING) aguardando
     * consentimento via magic link/notificação. A API pública não expõe
     * approve/reject — isso acontece fora do SDK.
     */
    public function createLinkRequest(string $contextId, array $payload): array
    {
        return $this->client->post("/v1/contexts/{$contextId}/link-requests", $payload);
    }
}
