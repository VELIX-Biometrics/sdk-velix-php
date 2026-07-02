<?php

declare(strict_types=1);

namespace Velix\Models;

class CheckinResult
{
    public function __construct(
        public readonly bool $passed,
        public readonly ?string $personId,
        public readonly ?string $personName,
        public readonly string $action,  // GRANTED | DENIED
        public readonly ?string $reason, // face_not_recognized | liveness_failed | access_denied
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            passed: (bool) ($data['passed'] ?? false),
            personId: $data['personId'] ?? null,
            personName: $data['personName'] ?? null,
            action: $data['action'] ?? 'DENIED',
            reason: $data['reason'] ?? null,
        );
    }
}
