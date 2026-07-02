<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class WebhooksModule
{
    public function __construct(private readonly VelixClient $client) {}

    /**
     * @deprecated PUT /v1/tenants/me/settings não existe na superfície de API key
     *             `/v1/api/*` (ver public-api.yaml, task #593). Configuração de webhook
     *             não é exposta nesta superfície hoje. Ver task #656.
     */
    public function configure(string $url, string $secret, array $events = []): never
    {
        throw new \RuntimeException(
            'WebhooksModule::configure() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * Valida assinatura de um webhook recebido.
     * Uso: WebhooksModule::validateSignature($payload, $signature, $secret)
     */
    public static function validateSignature(string $payload, string $signature, string $secret): bool
    {
        $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }
}
