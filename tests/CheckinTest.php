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

    public function testIdentifyMatched(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'match' => true,
                    'subjectId' => 'uuid-123',
                    'subjectName' => 'Ana Silva',
                    'liveness' => ['ok' => true],
                    'model' => 'adaface',
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->identify(base64_encode('fake-frame'));

        $this->assertTrue($result->matched);
        $this->assertSame('uuid-123', $result->subjectId);
        $this->assertSame('Ana Silva', $result->subjectName);
        $this->assertTrue($result->livenessOk);
    }

    public function testIdentifyNotMatched(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'match' => false,
                    'subjectId' => null,
                    'subjectName' => null,
                    'liveness' => ['ok' => true],
                    'model' => 'adaface',
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->identify(base64_encode('fake-frame'));

        $this->assertFalse($result->matched);
        $this->assertNull($result->subjectId);
    }

    public function testThrowsAuthExceptionOn401(): void
    {
        $this->expectException(AuthException::class);

        $client = $this->makeClient([
            new Response(401, [], json_encode(['message' => 'Unauthorized'])),
        ]);

        (new CheckinModule($client))->identify(base64_encode('frame'));
    }

    public function testQrThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        $client = $this->makeClient([]);

        (new CheckinModule($client))->qr('acme', '123456');
    }

    public function testPinThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        $client = $this->makeClient([]);

        (new CheckinModule($client))->pin('acme', '1234');
    }
}
