<?php

declare(strict_types=1);

namespace Velix\Models;

class DeletionRequestResult
{
    public function __construct(
        public readonly ?string $protocolNumber,
        public readonly ?string $message,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape (snake_case) — see DeletionRequestResponse in public-api.yaml.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            protocolNumber: $data['protocol_number'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}
