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

    public function testListReturnsEvents(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => [
                    'items' => [
                        ['id' => 'evt-1', 'name' => 'Tech Summit', 'status' => 'active'],
                    ],
                    'total' => 1, 'page' => 1, 'limit' => 20,
                ],
            ])),
        ]);

        $result = (new EventsModule($client))->list();

        $this->assertSame(1, $result->total);
        $this->assertCount(1, $result->items);
        $this->assertSame('evt-1', $result->items[0]->id);
    }

    public function testGetReturnsEvent(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'data' => ['id' => 'evt-1', 'name' => 'Tech Summit', 'status' => 'active'],
            ])),
        ]);

        $event = (new EventsModule($client))->get('evt-1');

        $this->assertSame('evt-1', $event->id);
        $this->assertSame('Tech Summit', $event->name);
    }

    public function testGetThrows404(): void
    {
        $this->expectException(VelixException::class);

        $client = $this->makeClient([
            new Response(404, [], json_encode(['message' => 'Event not found'])),
        ]);

        (new EventsModule($client))->get('nonexistent');
    }

    public function testCreateReturnsEvent(): void
    {
        $client = $this->makeClient([
            new Response(201, [], json_encode([
                'data' => ['id' => 'evt-new', 'name' => 'New Event', 'status' => 'draft'],
            ])),
        ]);

        $event = (new EventsModule($client))->create(['name' => 'New Event']);

        $this->assertSame('evt-new', $event->id);
        $this->assertSame('draft', $event->status);
    }
}
