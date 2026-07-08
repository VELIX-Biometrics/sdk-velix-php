<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class AuthorizationTokenModule
{
    public function __construct(private readonly VelixClient $client) {}

    /** POST /v1/authorization-tokens/validate — valida (e opcionalmente consome) um token vat_*. */
    public function validate(string $token, bool $consume = false): array
    {
        return $this->client->post('/v1/authorization-tokens/validate', ['token' => $token, 'consume' => $consume]);
    }
}
