<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Velix\VelixClient;
use Velix\Modules\LgpdModule;

class LgpdTest extends TestCase
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

    public function testRequestDeletionReturnsProtocol(): void
    {
        $client = $this->makeClient([
            new Response(201, [], json_encode([
                'data' => [
                    'protocol_number' => 'DEL-2026-0001',
                    'message' => 'Solicitação registrada',
                ],
            ])),
        ]);

        $result = (new LgpdModule($client))->requestDeletion('uuid-456');

        $this->assertSame('DEL-2026-0001', $result->protocolNumber);
    }
}
