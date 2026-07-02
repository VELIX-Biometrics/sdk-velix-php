<?php

declare(strict_types=1);

namespace Velix\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Velix\VelixClient;
use Velix\Modules\EventsModule;
use Velix\Exceptions\VelixException;

class EventsTest extends TestCase
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

    public function testCreateGuestReturnsGuest(): void
    {
        $client = $this->makeClient([
            new Response(201, [], json_encode([
                'data' => [
                    'id' => 'guest-1',
                    'eventId' => 'evt-1',
                    'name' => 'Maria Souza',
                    'email' => 'maria@example.com',
                    'status' => 'invited',
                    'categoryId' => null,
                ],
            ])),
        ]);

        $guest = (new EventsModule($client))->createGuest('evt-1', [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
        ]);

        $this->assertSame('guest-1', $guest->id);
        $this->assertSame('evt-1', $guest->eventId);
        $this->assertSame('invited', $guest->status);
    }

    public function testGetGuestReturnsGuest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'id' => 'guest-1',
                    'eventId' => 'evt-1',
                    'name' => 'Maria Souza',
                    'email' => 'maria@example.com',
                    'status' => 'checked_in',
                    'categoryId' => 'cat-1',
                ],
            ])),
        ]);

        $guest = (new EventsModule($client))->getGuest('evt-1', 'guest-1');

        $this->assertSame('checked_in', $guest->status);
        $this->assertSame('cat-1', $guest->categoryId);
    }

    public function testGetGuestThrows404(): void
    {
        $this->expectException(VelixException::class);

        $client = $this->makeClient([
            new Response(404, [], json_encode(['message' => 'Guest not found'])),
        ]);

        (new EventsModule($client))->getGuest('evt-1', 'nonexistent');
    }

    public function testListThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        $client = $this->makeClient([]);

        (new EventsModule($client))->list();
    }

    public function testCreateThrowsNotImplemented(): void
    {
        $this->expectException(\RuntimeException::class);

        $client = $this->makeClient([]);

        (new EventsModule($client))->create(['name' => 'New Event']);
    }
}
