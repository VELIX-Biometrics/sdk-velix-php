<?php

declare(strict_types=1);

namespace Velix\Models;

class Event
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $status,
        public readonly ?string $location,
        public readonly ?\DateTimeImmutable $startsAt,
        public readonly ?\DateTimeImmutable $endsAt,
        public readonly int $guestCount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            status: $data['status'] ?? 'draft',
            location: $data['location'] ?? null,
            startsAt: isset($data['startsAt']) ? new \DateTimeImmutable($data['startsAt']) : null,
            endsAt: isset($data['endsAt']) ? new \DateTimeImmutable($data['endsAt']) : null,
            guestCount: (int) ($data['guestCount'] ?? 0),
        );
    }
}
