<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;
use Velix\Models\Guest;

/**
 * Convidados de evento via API key (Velix Events).
 *
 * Contrato real (cobertura mínima): apenas criar e consultar convidado.
 *   POST /v1/api/events/{id}/guests        — escopo `events:write`
 *   GET  /v1/api/events/{id}/guests/{guestId} — escopo `events:read`
 *
 * Ver public-api.yaml (task #593) — nenhum outro endpoint de eventos existe
 * na superfície de API key hoje.
 */
class EventsModule
{
    public function __construct(private readonly VelixClient $client) {}

    /**
     * @param array<string, mixed> $attrs name/email obrigatórios; cpf, phone, birthDate,
     *                                     categoryId, companionOf opcionais.
     */
    public function createGuest(string $eventId, array $attrs): Guest
    {
        return Guest::fromArray($this->client->post("/v1/api/events/{$eventId}/guests", $attrs));
    }

    public function getGuest(string $eventId, string $guestId): Guest
    {
        return Guest::fromArray($this->client->get("/v1/api/events/{$eventId}/guests/{$guestId}"));
    }

    /**
     * @deprecated Endpoint GET /v1/events não existe na superfície de API key. Não implementado.
     */
    public function list(array $params = []): never
    {
        throw new \RuntimeException(
            'EventsModule::list() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * @deprecated Endpoint GET /v1/events/{id} não existe na superfície de API key. Não implementado.
     */
    public function get(string $id): never
    {
        throw new \RuntimeException(
            'EventsModule::get() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * @deprecated Endpoint POST /v1/events não existe na superfície de API key. Não implementado.
     */
    public function create(array $attrs): never
    {
        throw new \RuntimeException(
            'EventsModule::create() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * @deprecated Endpoint PATCH /v1/events/{id}/config não existe na superfície de API key. Não implementado.
     */
    public function configure(string $id, array $config): never
    {
        throw new \RuntimeException(
            'EventsModule::configure() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    /**
     * @deprecated Endpoint DELETE /v1/events/{id} não existe na superfície de API key. Não implementado.
     */
    public function delete(string $id): never
    {
        throw new \RuntimeException(
            'EventsModule::delete() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }
}
