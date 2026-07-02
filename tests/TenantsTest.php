<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Velix\VelixClient;
use Velix\Modules\TenantsModule;

class TenantsTest extends TestCase
{
    private function makeClient(array $responses): VelixClient
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $client = new VelixClient(['apiUrl' => 'http://localhost', 'apiKey' => 'test']);
        $ref = new \ReflectionProperty(VelixClient::class, 'http');
        $ref->setAccessible(true);
        $ref->setValue($client, new Client(['handler' => $stack, 'base_uri' => 'http://localhost']));
        return $client;
    }

    public function testMeReturnsTenant(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'tenant-uuid', 'name' => 'Acme Corp', 'slug' => 'acme',
                    'plan' => 'enterprise', 'maxPersons' => 1000,
                ],
            ])),
        ]);

        $tenant = (new TenantsModule($client))->me();

        $this->assertSame('tenant-uuid', $tenant->id);
        $this->assertSame('acme', $tenant->slug);
        $this->assertSame('enterprise', $tenant->plan);
    }

    public function testUpdateSettingsReturnsUpdatedTenant(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'tenant-uuid', 'requireLiveness' => true, 'timezone' => 'America/Sao_Paulo',
                ],
            ])),
        ]);

        $tenant = (new TenantsModule($client))->updateSettings([
            'requireLiveness' => true,
            'timezone' => 'America/Sao_Paulo',
        ]);

        $this->assertTrue($tenant->requireLiveness);
        $this->assertSame('America/Sao_Paulo', $tenant->timezone);
    }
}
