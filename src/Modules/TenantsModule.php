<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class TenantsModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function me(): array
    {
        return $this->client->get('/v1/tenants/me');
    }

    public function updateSettings(array $settings): array
    {
        return $this->client->put('/v1/tenants/me/settings', $settings);
    }
}
