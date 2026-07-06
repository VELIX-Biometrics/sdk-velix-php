<?php

declare(strict_types=1);

namespace Velix\Models;

class CheckinResult
{
    public function __construct(
        public readonly bool $matched,
        public readonly ?string $subjectId,
        public readonly ?string $subjectName,
        public readonly bool $livenessOk,
        public readonly ?string $model,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape real de CheckinService.identifyFace
     *                                    (checkin.service.ts): { match, subjectId, subjectName,
     *                                    liveness: { ok }, model }. Score de similaridade e de
     *                                    liveness nunca são incluídos por design.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            matched: (bool) ($data['match'] ?? false),
            subjectId: $data['subjectId'] ?? null,
            subjectName: $data['subjectName'] ?? null,
            livenessOk: (bool) ($data['liveness']['ok'] ?? false),
            model: $data['model'] ?? null,
        );
    }
}
