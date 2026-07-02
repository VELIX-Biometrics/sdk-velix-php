<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Velix\VelixClient;
use Velix\Modules\TenantsModule;
use Velix\Modules\WebhooksModule;

/**
 * TenantsModule e WebhooksModule::configure() não têm endpoint correspondente na
 * superfície de API key `/v1/api/*` (public-api.yaml, task #593) — todos os métodos
 * agora lançam RuntimeException. Ver task #656.
 */
class TenantsTest extends TestCase
{
    private function makeClient(): VelixClient
    {
        $mock = new MockHandler([]);
        $stack = HandlerStack::create($mock);
        $client = new VelixClient(['apiUrl' => 'http://localhost', 'apiKey' => 'test']);
        $ref = new \ReflectionProperty(VelixClient::class, 'http');
        $ref->setAccessible(true);
        $ref->setValue($client, new Client(['handler' => $stack, 'base_uri' => 'http://localhost']));
        return $client;
    }

    public function testMeThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        (new TenantsModule($this->makeClient()))->me();
    }

    public function testUpdateSettingsThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        (new TenantsModule($this->makeClient()))->updateSettings(['requireLiveness' => true]);
    }

    public function testWebhooksConfigureThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        (new WebhooksModule($this->makeClient()))->configure('https://example.com', 'secret');
    }

    public function testWebhooksValidateSignatureStillWorks(): void
    {
        $payload = '{"event":"test"}';
        $secret = 'shh';
        $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        $this->assertTrue(WebhooksModule::validateSignature($payload, $signature, $secret));
        $this->assertFalse(WebhooksModule::validateSignature($payload, 'sha256=bad', $secret));
    }
}
