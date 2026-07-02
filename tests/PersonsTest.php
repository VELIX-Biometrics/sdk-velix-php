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

    public function testCreatePerson(): void
    {
        $client = $this->makeClient([
            new Response(201, [], json_encode([
                'data' => [
                    'id' => 'uuid-456',
                    'name' => 'Maria Souza',
                    'email' => 'maria@example.com',
                    'document' => '123.456.789-00',
                    'status' => 'active',
                    'biometricEnrolled' => false,
                    'createdAt' => '2026-01-01T00:00:00Z',
                ],
            ])),
        ]);

        $person = (new PersonsModule($client))->create([
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
        ]);

        $this->assertSame('uuid-456', $person->id);
        $this->assertFalse($person->biometricEnrolled);
    }

    public function testEnroll(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['data' => ['enrolled' => true]])),
        ]);

        $result = (new PersonsModule($client))->enroll('uuid-456', [base64_encode('f1'), base64_encode('f2')]);

        $this->assertTrue($result);
    }
}
