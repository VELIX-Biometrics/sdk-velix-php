<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\Event;

class EventsModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function list(array $params = []): array
    {
        $data = $this->client->get('/v1/events', $params);
        return array_map(fn(array $e) => Event::fromArray($e), $data['items'] ?? $data);
    }

    public function get(string $id): Event
    {
        return Event::fromArray($this->client->get("/v1/events/{$id}"));
    }

    public function create(array $attrs): Event
    {
        return Event::fromArray($this->client->post('/v1/events', $attrs));
    }

    public function configure(string $id, array $config): Event
    {
        return Event::fromArray($this->client->patch("/v1/events/{$id}/config", $config));
    }

    public function delete(string $id): void
    {
        $this->client->delete("/v1/events/{$id}");
    }
}
