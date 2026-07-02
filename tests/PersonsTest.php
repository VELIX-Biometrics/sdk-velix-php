<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Velix\VelixClient;
use Velix\Modules\PersonsModule;
use Velix\Modules\OnboardingModule;
use Velix\Modules\MeModule;

class PersonsTest extends TestCase
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

    public function testPersonsModuleMethodsAreNotImplemented(): void
    {
        $client = $this->makeClient([]);
        $module = new PersonsModule($client);

        foreach (['list', 'get', 'create', 'update', 'delete', 'enroll'] as $method) {
            try {
                match ($method) {
                    'list' => $module->list(),
                    'get' => $module->get('id'),
                    'create' => $module->create([]),
                    'update' => $module->update('id', []),
                    'delete' => $module->delete('id'),
                    'enroll' => $module->enroll('id', []),
                };
                $this->fail("PersonsModule::{$method}() deveria lançar RuntimeException");
            } catch (\RuntimeException $e) {
                $this->assertNotEmpty($e->getMessage());
            }
        }
    }

    public function testOnboardingEnroll(): void
    {
        $client = $this->makeClient([
            new Response(201, [], json_encode([
                'data' => [
                    'person_id' => 'uuid-456',
                    'identity_id' => 'identity-1',
                    'enrolled' => true,
                    'frames_processed' => 3,
                    'frames_results' => [],
                    'embedding_id' => 'emb-1',
                    'message' => 'Onboarding concluído',
                ],
            ])),
        ]);

        $result = (new OnboardingModule($client))->enroll('Maria Souza', [
            base64_encode('f1'),
            base64_encode('f2'),
            base64_encode('f3'),
        ], ['email' => 'maria@example.com']);

        $this->assertSame('uuid-456', $result->personId);
        $this->assertTrue($result->enrolled);
        $this->assertSame(3, $result->framesProcessed);
    }

    public function testMeGet(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'uuid-456',
                    'name' => 'Maria Souza',
                    'email' => 'maria@example.com',
                    'phone' => null,
                    'photo_url' => null,
                    'created_at' => '2026-01-01T00:00:00Z',
                ],
            ])),
        ]);

        $me = (new MeModule($client))->get('uuid-456');

        $this->assertSame('uuid-456', $me->id);
        $this->assertSame('Maria Souza', $me->name);
    }
}
