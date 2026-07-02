<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Velix\VelixClient;
use Velix\Modules\CheckinModule;
use Velix\Exceptions\AuthException;

class CheckinTest extends TestCase
{
    private function makeClient(array $responses): VelixClient
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);

        $client = new VelixClient(['apiUrl' => 'http://localhost', 'apiKey' => 'test']);

        // Inject mock handler via reflection
        $ref = new \ReflectionProperty(VelixClient::class, 'http');
        $ref->setAccessible(true);
        $ref->setValue($client, new Client(['handler' => $stack, 'base_uri' => 'http://localhost']));

        return $client;
    }

    public function testFacialGranted(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'passed' => true,
                    'personId' => 'uuid-123',
                    'personName' => 'João Silva',
                    'action' => 'GRANTED',
                    'reason' => null,
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->facial('acme', base64_encode('fake-frame'));

        $this->assertTrue($result->passed);
        $this->assertSame('GRANTED', $result->action);
        $this->assertSame('uuid-123', $result->personId);
    }

    public function testFacialDenied(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'passed' => false,
                    'personId' => null,
                    'personName' => null,
                    'action' => 'DENIED',
                    'reason' => 'face_not_recognized',
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->facial('acme', base64_encode('fake-frame'));

        $this->assertFalse($result->passed);
        $this->assertSame('face_not_recognized', $result->reason);
    }

    public function testThrowsAuthExceptionOn401(): void
    {
        $this->expectException(AuthException::class);

        $client = $this->makeClient([
            new Response(401, [], json_encode(['message' => 'Unauthorized'])),
        ]);

        (new CheckinModule($client))->facial('acme', base64_encode('frame'));
    }
}
