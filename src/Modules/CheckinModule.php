<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\CheckinResult;
use Velix\Exceptions\BiometricException;

class CheckinModule
{
    public function __construct(private readonly VelixClient $client) {}

    /**
     * Identificação facial. Frame deve ser base64 de JPEG/PNG.
     */
    public function facial(string $tenantSlug, string $frameBase64, array $options = []): CheckinResult
    {
        $data = $this->client->post("/v1/public/checkin/{$tenantSlug}/identify", [
            'frame' => $frameBase64,
            'deviceId' => $options['deviceId'] ?? null,
            'eventId' => $options['eventId'] ?? null,
        ]);

        if (isset($data['error'])) {
            throw new BiometricException($data['error']);
        }

        return CheckinResult::fromArray($data);
    }

    /**
     * Check-in por QR code.
     */
    public function qr(string $tenantSlug, string $qrCode, array $options = []): CheckinResult
    {
        $data = $this->client->post("/v1/public/checkin/{$tenantSlug}/qr", [
            'code' => $qrCode,
            'deviceId' => $options['deviceId'] ?? null,
            'eventId' => $options['eventId'] ?? null,
        ]);

        return CheckinResult::fromArray($data);
    }

    /**
     * Check-in por PIN.
     */
    public function pin(string $tenantSlug, string $pin, array $options = []): CheckinResult
    {
        $data = $this->client->post("/v1/public/checkin/{$tenantSlug}/pin", [
            'pin' => $pin,
            'deviceId' => $options['deviceId'] ?? null,
            'eventId' => $options['eventId'] ?? null,
        ]);

        return CheckinResult::fromArray($data);
    }
}
