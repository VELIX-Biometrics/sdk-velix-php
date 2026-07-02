<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\Person;

class PersonsModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function list(array $params = []): array
    {
        $data = $this->client->get('/v1/persons', $params);
        return array_map(fn(array $p) => Person::fromArray($p), $data['items'] ?? $data);
    }

    public function get(string $id): Person
    {
        return Person::fromArray($this->client->get("/v1/persons/{$id}"));
    }

    public function create(array $attrs): Person
    {
        return Person::fromArray($this->client->post('/v1/persons', $attrs));
    }

    public function update(string $id, array $attrs): Person
    {
        return Person::fromArray($this->client->put("/v1/persons/{$id}", $attrs));
    }

    public function delete(string $id): void
    {
        $this->client->delete("/v1/persons/{$id}");
    }

    /**
     * Enroll biométrico — frames deve ser array de até 3 imagens base64.
     */
    public function enroll(string $id, array $frames): bool
    {
        $result = $this->client->post("/v1/persons/{$id}/enroll", ['frames' => $frames]);
        return (bool) ($result['enrolled'] ?? false);
    }
}
