<?php

declare(strict_types=1);

namespace Velix\Models;

class CheckinResult
{
    public function __construct(
        public readonly bool $matched,
        public readonly ?string $personId,
        public readonly ?float $qualityScore,
        public readonly ?string $message,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape (snake_case) — see CheckinIdentifyResponse
     *                                    in public-api.yaml. Liveness score is never included
     *                                    by design (only `matched` is exposed).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            matched: (bool) ($data['matched'] ?? false),
            personId: $data['person_id'] ?? null,
            qualityScore: isset($data['quality_score']) ? (float) $data['quality_score'] : null,
            message: $data['message'] ?? null,
        );
    }
}
