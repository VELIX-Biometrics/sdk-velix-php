<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\DeletionRequestResult;

/**
 * Solicitação de exclusão de dados — LGPD (Velix.ID).
 *
 * Contrato real: POST /v1/api/deletion-request — escopo `lgpd:write`.
 */
class LgpdModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function requestDeletion(string $personId): DeletionRequestResult
    {
        return DeletionRequestResult::fromArray(
            $this->client->post('/v1/api/deletion-request', ['person_id' => $personId])
        );
    }
}
