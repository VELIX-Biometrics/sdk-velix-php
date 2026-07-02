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
                    'matched' => true,
                    'person_id' => 'uuid-123',
                    'quality_score' => 0.92,
                    'message' => 'OK',
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->identify(base64_encode('fake-frame'));

        $this->assertTrue($result->matched);
        $this->assertSame('uuid-123', $result->personId);
        $this->assertSame(0.92, $result->qualityScore);
    }

    public function testIdentifyNotMatched(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'matched' => false,
                    'person_id' => null,
                    'quality_score' => 0.4,
                    'message' => 'face_not_recognized',
                ],
            ])),
        ]);

        $result = (new CheckinModule($client))->identify(base64_encode('fake-frame'));

        $this->assertFalse($result->matched);
        $this->assertSame('face_not_recognized', $result->message);
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
