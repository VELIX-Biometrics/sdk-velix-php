<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\CheckinResult;
use Velix\Exceptions\BiometricException;

/**
 * Identificação facial (checkin) via API key (Velix.ID).
 *
 * Contrato real: POST /v1/api/checkin/identify — escopo `checkin:write`.
 * Ver public-api.yaml (task #593) / CheckinIdentifyRequest / CheckinIdentifyResponse.
 *
 * Score de liveness NUNCA é exposto por esta API — apenas `passed`.
 */
class CheckinModule
{
    public function __construct(private readonly VelixClient $client) {}

    /**
     * @param array<int, string>       $images   Frames adicionais opcionais (além de $imageBase64).
     * @param array<string, mixed>     $options  Campos opcionais do CheckinIdentifyRequest: images,
     *                                            topK, liveness (['token' => ..., 'samples' => [...]]),
     *                                            location (['latitude' => ..., 'longitude' => ..., 'accuracy' => ...]).
     */
    public function identify(string $imageBase64, array $options = []): CheckinResult
    {
        $body = array_merge($options, [
            'imageBase64' => $imageBase64,
        ]);

        $data = $this->client->post('/v1/api/checkin/identify', $body);

        if (isset($data['error'])) {
            throw new BiometricException($data['error']);
        }

        return CheckinResult::fromArray($data);
    }

    /**
     * @deprecated Endpoint /v1/public/checkin/{tenantSlug}/qr não existe na superfície
     *             de API key (`/v1/api/*`) definida na spec #593. Não implementado.
     */
    public function qr(string $tenantSlug, string $qrCode, array $options = []): never
    {
        throw new \RuntimeException(
            'CheckinModule::qr() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * @deprecated Endpoint /v1/public/checkin/{tenantSlug}/pin não existe na superfície
     *             de API key (`/v1/api/*`) definida na spec #593. Não implementado.
     */
    public function pin(string $tenantSlug, string $pin, array $options = []): never
    {
        throw new \RuntimeException(
            'CheckinModule::pin() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }
}
