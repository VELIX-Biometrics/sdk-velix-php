<?php

declare(strict_types=1);

namespace Velix\Models;

class Person
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $email,
        public readonly ?string $document,
        public readonly string $status,
        public readonly bool $biometricEnrolled,
        public readonly \DateTimeImmutable $createdAt,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'] ?? null,
            document: $data['document'] ?? null,
            status: $data['status'] ?? 'active',
            biometricEnrolled: (bool) ($data['biometricEnrolled'] ?? false),
            createdAt: new \DateTimeImmutable($data['createdAt'] ?? 'now'),
        );
    }
}
