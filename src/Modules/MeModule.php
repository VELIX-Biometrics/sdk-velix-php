<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\Me;

/**
 * Consulta de dados próprios de uma pessoa via API key (Velix.ID).
 *
 * Contrato real: GET /v1/api/me/{personId} — escopo `me:read`.
 */
class MeModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function get(string $personId): Me
    {
        return Me::fromArray($this->client->get("/v1/api/me/{$personId}"));
    }
}
