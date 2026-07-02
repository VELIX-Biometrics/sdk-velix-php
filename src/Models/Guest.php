<?php

declare(strict_types=1);

namespace Velix\Models;

class Guest
{
    public function __construct(
        public readonly string $id,
        public readonly string $eventId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $status,
        public readonly ?string $categoryId,
    ) {}

    /**
     * @param array<string, mixed> $data Wire shape — see GuestResponse in public-api.yaml
     *                                    (id/eventId/categoryId are camelCase in the spec).
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            eventId: $data['eventId'],
            name: $data['name'],
            email: $data['email'],
            status: $data['status'] ?? '',
            categoryId: $data['categoryId'] ?? null,
        );
    }
}
