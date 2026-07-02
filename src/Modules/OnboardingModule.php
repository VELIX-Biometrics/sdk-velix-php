<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\OnboardingResult;

/**
 * Onboarding biométrico via API key (Velix.ID).
 *
 * Contrato real: POST /v1/api/onboarding — escopo `onboarding:write`.
 * Ver public-api.yaml (task #593) / OnboardingRequest / OnboardingResponse.
 */
class OnboardingModule
{
    public function __construct(private readonly VelixClient $client) {}

    /**
     * @param array<int, string>       $frames  Frames JPEG base64 (sem prefixo data URI), mínimo 1.
     * @param array<string, mixed>     $options Campos opcionais: email, phone, document, document_type,
     *                                           external_id, metadata, role, access_groups.
     */
    public function enroll(string $name, array $frames, array $options = []): OnboardingResult
    {
        $body = array_merge($options, [
            'name' => $name,
            'frames' => $frames,
        ]);

        return OnboardingResult::fromArray($this->client->post('/v1/api/onboarding', $body));
    }
}
