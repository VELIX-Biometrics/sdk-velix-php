<?php

declare(strict_types=1);

namespace Velix\Models;

class Me
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $photoUrl,
        public readonly ?\DateTimeImmutable $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape (snake_case) — see MeResponse in public-api.yaml.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            photoUrl: $data['photo_url'] ?? null,
            createdAt: isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : null,
        );
    }
}
