<?php

declare(strict_types=1);

namespace Velix\Models;

class OnboardingResult
{
    /**
     * @param array<int, array<string, mixed>> $framesResults
     */
    public function __construct(
        public readonly ?string $personId,
        public readonly ?string $identityId,
        public readonly bool $enrolled,
        public readonly int $framesProcessed,
        public readonly array $framesResults,
        public readonly ?string $embeddingId,
        public readonly ?string $message,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape (snake_case) — see OnboardingResponse in public-api.yaml.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            personId: $data['person_id'] ?? null,
            identityId: $data['identity_id'] ?? null,
            enrolled: (bool) ($data['enrolled'] ?? false),
            framesProcessed: (int) ($data['frames_processed'] ?? 0),
            framesResults: $data['frames_results'] ?? [],
            embeddingId: $data['embedding_id'] ?? null,
            message: $data['message'] ?? null,
        );
    }
}
