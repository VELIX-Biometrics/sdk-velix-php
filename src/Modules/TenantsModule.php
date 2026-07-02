<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

/**
 * @deprecated GET /v1/tenants/me e PUT /v1/tenants/me/settings não existem na superfície
 *             de API key `/v1/api/*` (ver public-api.yaml, task #593). Não há equivalente
 *             hoje — o tenant é resolvido implicitamente a partir da API key em cada
 *             endpoint real. Mantido apenas para não quebrar autoload; todos os métodos
 *             lançam RuntimeException. Ver task #656.
 */
class TenantsModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function me(): never
    {
        throw new \RuntimeException(
            'TenantsModule::me() aponta para um endpoint que não existe na API real — '
            . 'o tenant é resolvido implicitamente pela API key. Ver public-api.yaml (task #593) e task #656.'
        );
    }

    public function updateSettings(array $settings): never
    {
        throw new \RuntimeException(
            'TenantsModule::updateSettings() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }
}
