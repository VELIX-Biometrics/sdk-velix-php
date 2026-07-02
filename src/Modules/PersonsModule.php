<?php

declare(strict_types=1);

namespace Velix\Modules;

use Velix\VelixClient;

/**
 * @deprecated Nenhum destes endpoints existe na superfície real `/v1/api/*`
 *             (ver public-api.yaml, task #593). Cadastro biométrico é feito via
 *             OnboardingModule::enroll() (POST /v1/api/onboarding) e consulta de
 *             dados via MeModule::get() (GET /v1/api/me/{personId}). Esta classe
 *             é mantida apenas para não quebrar o autoload de consumidores antigos
 *             — todos os métodos lançam RuntimeException. Ver task #656.
 */
class PersonsModule
{
    public function __construct(private readonly VelixClient $client) {}

    public function list(array $params = []): never
    {
        throw new \RuntimeException(
            'PersonsModule::list() aponta para um endpoint que não existe na API real — '
            . 'use MeModule::get() ou consulte a spec (public-api.yaml, task #593).'
        );
    }

    public function get(string $id): never
    {
        throw new \RuntimeException(
            'PersonsModule::get() foi substituído por MeModule::get() '
            . '(GET /v1/api/me/{personId}) — ver public-api.yaml (task #593) e task #656.'
        );
    }

    public function create(array $attrs): never
    {
        throw new \RuntimeException(
            'PersonsModule::create() aponta para um endpoint que não existe na API real — '
            . 'use OnboardingModule::enroll() (POST /v1/api/onboarding).'
        );
    }

    public function update(string $id, array $attrs): never
    {
        throw new \RuntimeException(
            'PersonsModule::update() aponta para um endpoint que não existe na API real — '
            . 'ver public-api.yaml (task #593) e task #656.'
        );
    }

    public function delete(string $id): never
    {
        throw new \RuntimeException(
            'PersonsModule::delete() aponta para um endpoint que não existe na API real — '
            . 'use LgpdModule::requestDeletion() (POST /v1/api/deletion-request).'
        );
    }

    /**
     * @deprecated Endpoint /v1/persons/{id}/enroll não existe. Use OnboardingModule::enroll()
     *             (POST /v1/api/onboarding).
     */
    public function enroll(string $id, array $frames): never
    {
        throw new \RuntimeException(
            'PersonsModule::enroll() foi substituído por OnboardingModule::enroll() '
            . '(POST /v1/api/onboarding) — ver public-api.yaml (task #593) e task #656.'
        );
    }
}
