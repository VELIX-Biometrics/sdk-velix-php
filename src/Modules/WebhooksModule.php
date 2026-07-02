<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

class WebhooksModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function configure(string $url, string $secret, array $events = []): array
    {
        return $this->client->put('/v1/tenants/me/settings', [
            'webhookUrl' => $url,
            'webhookSecret' => $secret,
            'webhookEvents' => $events,
        ]);
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
